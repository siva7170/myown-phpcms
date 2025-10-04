<?php
/* @var $t root\models\UserLogin */
echo "This is ".print_r($t->user_name,true).'<br/>';
echo $this->viewPartial('shared/indexshared',['x'=>$t]);
echo $this->pageTitle.'<br/>';
