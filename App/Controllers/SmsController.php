<?php

namespace App\Controllers;

use App\Entities\Bid;
use App\Repositories\BidRepository;
use App\Repositories\ItemRepository;
use App\Request;
use App\UseCases\ParseBidMessage;
use Exception;
use Psr\Http\Message\ResponseInterface;

class SmsController extends Controller
{
    protected ItemRepository $itemRepository;

    protected BidRepository $bidRepository;

    public function __construct(ItemRepository $itemRepository, BidRepository $bidRepository)
    {
        $this->itemRepository = $itemRepository;
        $this->bidRepository = $bidRepository;
    }

    public function incoming(Request $request): ResponseInterface
    {
        $bid = Bid::create()
            ->setRecipient($request->input('shortnumber'))
            ->setSender($request->input('number'))
            ->setOriginalMessage(trim($request->input('prefix') . ' ' . $request->input('msg')))
            ->setTimestamp(millitime());

        $bidMeta = perform(ParseBidMessage::class, [
            'message' => $bid->getOriginalMessage(),
        ]);

        $bid->setAmount($bidMeta['amount']);

        if ($item = $this->itemRepository->findByKey($bidMeta['item_key'])) {
            $bid->setItemId($item->getId());
        }

        if (!$this->bidRepository->save($bid)) {
            throw new Exception('Failed to save bid :(');
        }

        return response()->json(
            $bid
        );
    }
}
