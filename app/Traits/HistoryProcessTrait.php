<?php

namespace App\Traits;

use App\Models\History;
use Illuminate\Support\Facades\Log;

trait HistoryProcessTrait{

    public static function StoreHistory(int $userId, array $request) : int|\Exception {

        try {

            $Data = [
                'user_id'       => $userId,
                'action'        => $request['action'],
                'item'          => $request['item'],
                'item_id'       => $request['item_id'],
                'description'   => $request['description'],
                'created_at'    => now(),
                'updated_at'    => now(),
            ];

            $NewHistory          = new History();
            $NewHistory->item    = $Data['item'];
            $NewHistory->item_id = $Data['item_id'];

            if($Data['action'] == 'create') $NewHistory->history = json_encode([$Data], JSON_UNESCAPED_UNICODE);
            if(in_array($Data['action'], ['update','delete'])){

                    $OldHistory = History::where('item', '=', $Data['item'])
                        ->where('item_id', '=', $Data['item_id'])
                        ->orderBy('id', 'DESC')
                        ->first();

                    $OldHistoryData = json_decode($OldHistory->history, true);

                    $OldHistoryData[] = $Data;

                    $NewHistory->history = json_encode($OldHistoryData, JSON_UNESCAPED_UNICODE);

                    $UpdateHistory = History::where('id', '=', $OldHistory->id)
                        ->update(['history' => $NewHistory->history]);

                    return $OldHistory->id;
            }

            $NewHistory->save();

            return $NewHistory->id;

        }catch (\Exception $e) {

            return $e;

        }

    }

}
