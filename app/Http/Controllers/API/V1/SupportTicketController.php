<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Requests\SuperAdmin\Ticket\CreateTicket;
use App\Http\Requests\SuperAdmin\Ticket\Reply;
use App\Http\Requests\SuperAdmin\Ticket\TicketFilterRequest;
use App\Http\Resources\SupportTicketResource;
use App\Interfaces\SupportTicketInterface;
use App\Models\SupportTicket;
use App\Services\SuperAdmin\SupportTicketService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class SupportTicketController extends BaseController
{
    /**
     * @var SupportTicketInterface
     */
    private $supportTicketRepository;

    /**
     * @var SupportTicketService
     */
    private $supportTicketService;

    public function __construct(
        SupportTicketInterface $supportTicketRepository,
        SupportTicketService $supportTicketService
    ) {
        $this->supportTicketRepository = $supportTicketRepository;
        $this->supportTicketService = $supportTicketService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/support-tickets",
     *     summary="Create a new Ticket",
     *     tags={"Support Tickets"},
     *     @OA\Parameter(name="X-Api-Key", required=true, in="header"),
     *     @OA\Parameter(name="X-User-Id", required=true, in="header"),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="ticket", type="object",
     *                 @OA\Property(property="subject", example="Help"),
     *                 @OA\Property(property="receive_notifications", type="bool", example="true"),
     *                 @OA\Property(property="category", example="Plans"),
     *             ),
     *             @OA\Property(property="message", type="object",
     *                 @OA\Property(property="body", example="This is a message")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", example=true),
     *             @OA\Property(property="data", ref="#/components/schemas/SupportTicket")
     *         )
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
     *         response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="bool", example="false"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(example="message.body field is required.")
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
     * @param CreateTicket $request
     *
     * @return JsonResponse
     */
    public function store(CreateTicket $request): JsonResponse
    {
        $supportTicket = $this->supportTicketRepository->storeSupportTicket($request->client, $request->all());

        $supportTicket->load('messages');

        if ($supportTicket instanceof SupportTicket) {
            return $this->sendResponse($supportTicket);
        }

        return response()->json([
            'success' => false,
        ], 500);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/support-tickets/query",
     *     summary="Support Tickets Query",
     *     tags={"Support Tickets"},
     *     @OA\Parameter(name="X-Api-Key", required=true, in="header"),
     *     @OA\Parameter(name="X-User-Id", required=true, in="header"),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="include_closed", example=true),
     *             @OA\Property(property="category", example="Plans"),
     *             @OA\Property(property="subject", example="Help"),
     *             @OA\Property(property="create_date", type="object",
     *                 @OA\Property(property="from", example="2022-01-10"),
     *                 @OA\Property(property="until", example="2022-01-10")
     *             ),
     *             @OA\Property(property="last_update", type="object",
     *                 @OA\Property(property="from", example="2022-01-10"),
     *                 @OA\Property(property="until", example="2022-01-10")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/SupportTicket")
     *             )
     *         )
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
     *         response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="bool", example="false"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(example="message.body field is required.")
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
     * @param TicketFilterRequest $request
     *
     * @return JsonResponse
     */
    public function getFiltered(TicketFilterRequest $request): JsonResponse
    {
        $email = $request->client->email ?? null;
        $includeClosed = (bool) $request->input('include_closed');
        $category = $request->input('category');
        $subject = $request->input('subject');
        $createDate = null;
        $lastUpdate = null;

        if ($request->has('create_date.from')) {
            $createDate = [
                Carbon::createFromFormat('Y-m-d', $request->input('create_date.from')),
                Carbon::createFromFormat('Y-m-d', $request->input('create_date.until')),
            ];
        }
        if ($request->has('last_update.from')) {
            $lastUpdate = [
                Carbon::createFromFormat('Y-m-d', $request->input('last_update.from')),
                Carbon::createFromFormat('Y-m-d', $request->input('last_update.until')),
            ];
        }

        $tickets = $this->supportTicketRepository->getFilteredSupportTicketsOfUser(
            $email,
            $includeClosed,
            $category,
            $subject,
            $createDate,
            $lastUpdate
        );

        return $this->sendCollectionResponse($tickets);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/support-tickets/{support_ticket}",
     *     summary="Show Single Support Ticket",
     *     tags={"Support Tickets"},
     *     @OA\Parameter(name="support_ticket", required=true, in="path"),
     *     @OA\Parameter(name="X-Api-Key", required=true, in="header"),
     *     @OA\Parameter(name="X-User-Id", required=true, in="header"),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/SupportTicket")
     *             )
     *         )
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
     * @param SupportTicket $supportTicket
     *
     * @return JsonResponse
     */
    public function show(SupportTicket $supportTicket): JsonResponse
    {
        $supportTicket->load('messages.attachments');
        $supportTicket->messages()->update(['is_read_by_client' => true]);

        return $this->sendResponse($supportTicket);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/support-tickets/{support_ticket}/reply",
     *     summary="Support Tickets Query",
     *     description="files.* needs to be binary",
     *     tags={"Support Tickets"},
     *     @OA\Parameter(name="support_ticket", required=true, in="path"),
     *     @OA\Parameter(name="X-Api-Key", required=true, in="header"),
     *     @OA\Parameter(name="X-User-Id", required=true, in="header"),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="body", type="string", example="This is an example response"),
     *             @OA\Property(property="files", type="array",
     *                 @OA\Items(
     *                     type="file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", example=true),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(ref="#/components/schemas/SupportTicket")
     *             )
     *         )
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
     *         response=422, description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="bool", example="false"),
     *             @OA\Property(property="errors", type="array",
     *                 @OA\Items(example="message.body field is required.")
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
     * @param SupportTicket $supportTicket
     * @param Reply         $request
     *
     * @return JsonResponse
     */
    public function reply(SupportTicket $supportTicket, Reply $request): JsonResponse
    {
        try {
            $supportTicket = $this->supportTicketService->storeReply($supportTicket, $request->validated());
        } catch (\Exception $exception) {
            return error_response([
                $exception->getMessage(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->sendResponse($supportTicket);
    }

    /**
     * @return string
     */
    protected function resource(): string
    {
        return SupportTicketResource::class;
    }
}
