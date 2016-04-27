<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\bpm\models;

use Yii;

/**
 * This is the model class for table "meican_bpm_node".
 *
 * @property integer $id
 * @property integer $workflow_id
 * @property string $type
 * @property string $operator
 * @property string $value
 * @property integer $index
 * @property integer $output_yes
 * @property integer $output_no
 *
 * @property BpmFlowControl[] $bpmFlowControls
 * @property BpmWorkflow $workflow
 * @property BpmNode $outputNo
 * @property BpmNode[] $bpmNodes
 * @property BpmNode $outputYes
 * @property BpmNode[] $bpmNodes0
 */
class BpmNode extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meican_bpm_node';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['workflow_id', 'type', 'index'], 'required'],
            [['workflow_id', 'index', 'output_yes', 'output_no'], 'integer'],
            [['type', 'operator', 'value'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'workflow_id' => 'Workflow ID',
            'type' => 'Type',
            'operator' => 'Operator',
            'value' => 'Value',
            'index' => 'Index',
            'output_yes' => 'Output Yes',
            'output_no' => 'Output No',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBpmFlowControls()
    {
        return $this->hasMany(BpmFlowControl::className(), ['node_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWorkflow()
    {
        return $this->hasOne(BpmWorkflow::className(), ['id' => 'workflow_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutputNo()
    {
        return $this->hasOne(BpmNode::className(), ['id' => 'output_no']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBpmNodes()
    {
        return $this->hasMany(BpmNode::className(), ['output_no' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutputYes()
    {
        return $this->hasOne(BpmNode::className(), ['id' => 'output_yes']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBpmNodes0()
    {
        return $this->hasMany(BpmNode::className(), ['output_yes' => 'id']);
    }
}
