<?php 

class DocController implements ContentController
{
    //returns all extensions registered by this type of content
    public function getRegisteredExtensions(){return array('vnd.openxmlformats-officedocument.wordprocessingml.document');}

    public function handleHash($hash,$url)
    {
        $path = ROOT.DS.'data'.DS.$hash.DS.$hash;

        if(in_array('raw',$url))
        {
            header('Content-Type: text/plain; charset=utf-8');
            echo file_get_contents($path);
        }
        else if(in_array('download',$url))
        {
            if (file_exists($path)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Disposition: attachment; filename="'.basename($path).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit;
            }
        }
        else
            renderTemplate('text',array('hash'=>$hash,'content'=>htmlentities(file_get_contents($path))));
    }

    public function handleUpload($tmpfile,$hash=false)
    {
        if($hash===false)
        {
            $hash = getNewHash('docx',6);
        }
        else
        {
            if(!endswith($hash,'.docx'))
                $hash.='.docx';
            if(isExistingHash($hash))
                return array('status'=>'err','hash'=>$hash,'reason'=>'Custom hash already exists');
        }

        storeFile($tmpfile,$hash,true);
        
        return array('status'=>'ok','hash'=>$hash,'url'=>URL.$hash);
    }

    function getTypeOfText($hash)
    {
        return file_get_contents(ROOT.DS.'data'.DS.$hash.DS.'type');
    }
}