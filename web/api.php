<?php 
define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(__FILE__));

error_reporting(E_ALL || ~E_NOTICE);
ini_set('display_errors', 1);

include_once(ROOT.DS.'inc'.DS.'core.php');

$action = $_REQUEST['a'];
$email = $_REQUEST['email'];

switch($action)
{
    case 'getdoms':
        $settings = loadSettings();
        if($settings['DOMAINS'])
            $o = explode(',',$settings['DOMAINS']);
    break;
    case 'attachment':
        $id = $_REQUEST['id'];
        $filename = $_REQUEST['filename'];
        $filepath = ROOT.DS.'..'.DS.'data'.DS.$email.DS.'attachments'.DS.$id.'-'.$filename;
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $o = array('status'=>'err','reason'=>'Invalid Email address');
        else if(!is_dir(ROOT.DS.'..'.DS.'data'.DS.$email))
            $o = array('status'=>'err','reason'=>'No emails received on this address');
        else if(!is_numeric($id) || !emailIDExists($email,$id))
            $o = array('status'=>'err','reason'=>'Invalid Email ID');
        else if(!file_exists($filepath))
            $o = array('status'=>'err','reason'=>'File not found');
        else
        {
            header('Content-Type: '.mime_content_type($filepath));
            readfile($filepath);
            exit();
        }
    break;

    case 'load':
        $id = $_REQUEST['id'];
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $o = array('status'=>'err','reason'=>'Invalid Email address');
        else if(!is_dir(ROOT.DS.'..'.DS.'data'.DS.$email))
            $o = array('status'=>'err','reason'=>'No emails received on this address');
        else if(!is_numeric($id) || !emailIDExists($email,$id))
            $o = array('status'=>'err','reason'=>'Invalid Email ID');
        else
        {
            $data = getEmail($email,$id);
            $o = array('status'=>'ok','data'=>$data);
        }
    break;

    case 'list':
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
            $o = array('status'=>'err','reason'=>'Invalid Email address');
        else if(!is_dir(ROOT.DS.'..'.DS.'data'.DS.$email))
            $o = array('status'=>'ok','emails'=>[]);
        else
        {
            $data = getEmailsOfEmail($email);
            $o = array('status'=>'ok','emails'=>$data);
        }
    break;
}

echo json_encode($o);
//var_dump($o);