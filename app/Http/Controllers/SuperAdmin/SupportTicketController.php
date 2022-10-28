<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\Ticket\Reply;
use App\Models\SupportTicket;
use App\Models\TicketMessageAttachment;
use App\Services\SuperAdmin\SupportTicketService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * @var SupportTicketService
     */
    private $supportTicketService;

    public function __construct(SupportTicketService $supportTicketService)
    {
        $this->supportTicketService = $supportTicketService;
    }

    /**
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     *
     * @return View
     */
    public function index(): View
    {
        $filters = request()->get('filter');
        $includeClosed = filter_var(Arr::get($filters, 'with_closed'), FILTER_VALIDATE_BOOL);
        $unreadOnly = filter_var(Arr::get($filters, 'unread_only'), FILTER_VALIDATE_BOOL);
        $senderName = Arr::get($filters, 'sender_name');
        $supportTicketsQuery = SupportTicket::query();

        if (!$includeClosed) {
            $supportTicketsQuery->whereNull('closed_at');
        }

        if ($senderName) {
            $supportTicketsQuery->where('creator_name', 'LIKE', $senderName . '%');
        }

        if ($unreadOnly) {
            $supportTicketsQuery->whereHas('messages', function ($query) {
                $query->where('is_read_by_admin', false);
            });
        }

        $supportTickets = $supportTicketsQuery
            ->orderBy('updated_at', 'DESC')
            ->paginate()
            ->appends(request()->query());

        return view('super_admin.support_tickets.index', compact('supportTickets'));
    }

    /**
     * @param SupportTicket $support_ticket
     *
     * @return View
     */
    public function show(SupportTicket $support_ticket): View
    {
        $support_ticket->messages()->update(['is_read_by_admin' => true]);

        return view('super_admin.support_tickets.show', compact('support_ticket'));
    }

    /**
     * @param SupportTicket $supportTicket
     *
     * @return RedirectResponse
     */
    public function close(SupportTicket $supportTicket): RedirectResponse
    {
        $supportTicket->closed_at = now();
        $supportTicket->save();

        session()->flash('alert-success', __('messages.ticket_closed'));

        return redirect()->route('super_admin.support_tickets.show', $supportTicket);
    }

    /**
     * @param SupportTicket $support_ticket
     * @param Reply         $request
     *
     * @return \Illuminate\Http\JsonResponse|RedirectResponse
     */
    public function reply(SupportTicket $support_ticket, Reply $request)
    {
        try {
            $this->supportTicketService->storeReply($support_ticket, $request->validated(), $request->user()->id);

            session()->flash('alert-success', __('messages.support_ticket_replied'));
        } catch (\Exception $exception) {
            session()->flash('alert-error', $exception->getMessage());
        }

        if (!$request->wantsJson()) {
            return redirect()->route('super_admin.support_tickets.show', $support_ticket);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/support-tickets/{support_ticket}/attachments/{attachment}",
     *     summary="Download an attachment from a support ticket message",
     *     tags={"Support Tickets - Attachments"},
     *     @OA\Parameter(name="support_ticket", required=true, in="path"),
     *     @OA\Parameter(name="attachment", required=true, in="path"),
     *     @OA\Parameter(name="X-Api-Key", required=true, in="header"),
     *     @OA\Parameter(name="X-User-Id", required=true, in="header"),
     *     @OA\Response(
     *         response=200,
     *         description="File (binary)"
     *     ),
     *     @OA\Response(
     *         response=401, description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="bool", example="false"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(example="Unauthenticated")
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500, description="Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="bool", example="false")
     *         )
     *     )
     * )
     *
     * @param SupportTicket           $supportTicket
     * @param TicketMessageAttachment $attachment
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return Response
     */
    public function downloadAttachment(SupportTicket $supportTicket, TicketMessageAttachment $attachment): Response
    {
        $download = Storage::disk($attachment->storage_disk)
            ->get($attachment->storage_path);

        return new Response($download, 200, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => sprintf('attachment; filename="%s"', $attachment->file_name),
            'Content-Length' => $attachment->size,
        ]);
    }
}
