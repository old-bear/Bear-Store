<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "area".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $name
 * @property string $short_name
 * @property float $longitude
 * @property float $latitude
 * @property int $level
 * @property int $sort
 * @property int $status
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'area';
    }

    public function getUpperLevel()
    {
        return $this->hasOne(Area::className(), ['id' => 'parent_id']);
    }

    public function getLowerLevels()
    {
        return $this->hasMany(Area::className(), ['parent_id' => 'id']);
    }

    public function toString()
    {
        $node = $this;
        $last = $this->name;
        $ret = $last;
        while ($node->level > 1) {
            $node = $node->upperLevel;
            Yii::trace($last, $node->name);
            if ($last != $node->name) {
                $ret = $node->name . ' ' . $ret;
                $last = $node->name;
            }
        }
        return $ret;
    }
}
