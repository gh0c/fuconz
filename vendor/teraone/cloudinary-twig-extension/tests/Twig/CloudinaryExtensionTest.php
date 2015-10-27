<?php


use Teraone\Twig\Extension\CloudinaryExtension;

class CloudinaryExtensionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $ext = new CloudinaryExtension();
        $this->assertEquals('cloudinary', $ext->getName());
    }

    public function testCl_upload_url()
    {
        Cloudinary::config_from_url(CLOUDINARY_URL);
        $ext = new CloudinaryExtension();
        $cloudName = Cloudinary::option_get(Cloudinary::config(),'cloud_name');
        $this->assertEquals('https://api.cloudinary.com/v1_1/'.$cloudName.'/auto/upload', $ext->cl_upload_url());
    }

}
