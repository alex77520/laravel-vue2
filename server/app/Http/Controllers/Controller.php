<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	
	public function batchUpdateById($model, $ids, $updateColumn, $updateValue) {
		
		$affectedRows = $model::whereIn('id', $ids)->update([$updateColumn => $updateValue]);
		if($affectedRows !== count($ids)) {
			$successModels = $model::whereIn('id', $ids)->where($updateColumn, '=', $updateValue)->get();
			foreach($successModels as $successedIds) {
				$successedIds[] = $successedIds->id;
			}
		} else {
			$successedIds = $ids;
		}
		return $successedIds;
	
	}
}
