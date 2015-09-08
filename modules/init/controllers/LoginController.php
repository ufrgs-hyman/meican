<?php

namespace app\modules\init\controllers;

use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use app\models\User;
use app\models\Group;
use app\models\Preference;
use app\modules\init\models\LoginForm;
use app\modules\init\models\CafeUserForm;
use app\modules\init\models\ForgotPasswordForm;
use app\modules\aaa\models\AaaPreference;


class LoginController extends Controller {
	
	public $layout = 'loginLayout';
	
	private $_id;
	
	public function actionIndex() {
		if (!\Yii::$app->user->isGuest) {
			return $this->goHome();
		}
		
        $model = new LoginForm;
        
        if($model->load($_POST)) {
        	if($model->login()) {
        		return $this->goHome();
        	}
        }
        	
        return $this->render('login', array(
          	'model'=>$model,
        	'federation' => AaaPreference::isFederationEnabled(),
        ));
	}
	 
	public function actionLogout() {
		Yii::$app->user->logout();
		return $this->goHome();
	}
	
	public function actionPassword() {
		$model = new ForgotPasswordForm;
		
		Yii::trace($_POST);
			
		if($model->load($_POST)) {
			if(isset($_POST['g-recaptcha-response'])) $captcha=$_POST['g-recaptcha-response'];
			if(!$captcha){
				$model->addError($model->login, Yii::t('init', 'Please, check the captcha'));
				return $this->render('forgotPassword', array('model'=>$model));
			}
			$response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LdhOQgTAAAAAJ1f7mAaPpNvNHaGkZcKW8CPVIxv&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
			if($response){
				if($model->checkCamps()){
					if($model->sendEmail()){
						return $this->redirect('index');
					}
					else {
						return $this->render('forgotPassword', array('model'=>$model));
					}
				}
				else{
					return $this->render('forgotPassword', array('model'=>$model));
				}
			}
			else {
				$model->addError($model->login, Yii::t('init', 'Please, check the captcha'));
				return $this->render('forgotPassword', array('model'=>$model));
			}
		}
		
		return $this->render('forgotPassword', array('model'=>$model));
	}
	
	public function actionCafe() {
		$cafeUser = new CafeUserForm;
		if ($cafeUser->load($_POST) && $cafeUser->validate()) {
			$user = new User;
			$data = Yii::$app->session["data_from_cafe"];
			$data = json_decode($data);
			$user->setFromData($cafeUser->login, $cafeUser->password, $data->name,
				$data->email, Preference::findOneValue(Preference::FEDERATION_GROUP), Preference::findOneValue(Preference::FEDERATION_DOMAIN));
			if($user->save()) {
				$loginForm = new LoginForm;
			 	$loginForm->createSession($user);
			 	return $this->goHome();
			} else {
				foreach($user->getErrors() as $attribute => $error) {
    				$cafeUser->addError('', $error[0]);
    			}

				return $this->render('createCafeUser', array('model'=>$cafeUser));
			}
		}

		$data = Yii::$app->session["data_from_cafe"];
		if ($data) {
			$data = json_decode($data);
			$user = User::findOneByEmail($data->email);
			if ($user) {
				$loginForm = new LoginForm;
			 	$loginForm->createSession($user);
			 	return $this->goHome();
			} else {
				return $this->render('createCafeUser', array('model'=>$cafeUser));
			}
		} 
		return $this->goHome();
	}

}
?>