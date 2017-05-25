<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\McLibs\Classes\IdFactory;
use App\Models\Discuss;
use App\Models\Viewpoint;


class Notification extends Model
{
    protected $table = 'notification';
    protected $connection = 'm17web';

    public static function batchInsert($ids, $type) {
        $data = [];
        if($type === 'discuss') {
            $discuss = Discuss::getModelByIds($ids);
            foreach($discuss as $model) {
                $data[] = [
                    'sender_id'         => 'system',
                    'receiver_id'       => $model->user_id,
                    'notification_id'   => IdFactory::nextId(),
                    'type'              => 0,
                    'discuss_id'        => $model->discuss_id,
                    'discuss_title'     => $model->title,
                    'created_at'        => date('Y-m-d H:i:s'),
                ];
            }
        } elseif($type === 'viewpoint') {
            $viewpoint = Viewpoint::with('discuss', 'viewpoint')->whereIn('viewpoint_id', $ids)->get();
            foreach($viewpoint as $model) {
                if($model->parent_id != 0) {
                    $data[] = [
                        'sender_id'         => 'system',
                        'receiver_id'       => $model->user_id,
                        'notification_id'   => IdFactory::nextId(),
                        'type'              => 0,
                        'discuss_id'        => $model->discuss_id,
                        'discuss_title'     => $model->discuss->title ?? 'system message',
                        'viewpoint_id'      => $model->parent_id,
                        'viewpoint_content' => $model->viewpoint->content ?? 'system message',
                        'reply_id'          => $model->viewpoint_id,
                        'reply_content'     => $model->content,
                        'created_at'        => date('Y-m-d H:i:s'),
                    ];
                } else {
                     $data[] = [
                        'sender_id'         => 'system',
                        'receiver_id'       => $model->user_id,
                        'notification_id'   => IdFactory::nextId(),
                        'type'              => 0,
                        'discuss_id'        => $model->discuss_id,
                        'discuss_title'     => $model->discuss->title ?? 'system message',
                        'viewpoint_id'      => $model->viewpoint_id,
                        'viewpoint_content' => $model->content,
                        'created_at'        => date('Y-m-d H:i:s'),
                    ];
                }
               
            }
        }

        if(Notification::insert($data)) {
            return true;
        } else {
            return false;
        }


    }
}
