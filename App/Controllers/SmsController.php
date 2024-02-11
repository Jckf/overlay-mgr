<?php

namespace App\Controllers;

use App\Entities\Bid;
use App\Repositories\BidRepository;
use App\Repositories\ItemRepository;
use App\Request;
use App\UseCases\ParseBidMessage;
use Exception;

class SmsController extends Controller
{
    public function incoming(Request $request)
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

        /** @var ItemRepository $itemRepo */
        $itemRepo = container(ItemRepository::class);

        if ($item = $itemRepo->findByKey($bidMeta['item_key'])) {
            $bid->setItemId($item->getId());
        }

        /** @var BidRepository $bidRepo */
        $bidRepo = container(BidRepository::class);

        if (!$bidRepo->save($bid)) {
            throw new Exception('Failed to save bid :(');
        }

        header('Content-Type: application/json');

        echo json_encode($bid, JSON_PRETTY_PRINT);
    }
}
