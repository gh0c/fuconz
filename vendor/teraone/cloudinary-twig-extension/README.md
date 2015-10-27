Cloudinary extension for TWIG
=========================
Current status: BETA

This package provides the Cloudinary helper functions to twig. 


Installation
====

    composer require teraone/cloudinary-twig-extension


You will need to define your cloudinary configuration first

    Cloudinary::config_from_url('ENTER YOUR CLOUDINARY URL HERE');
    
    $twig = new Twig_Environment($loader);
    $twig->addExtension(new Teraone\Twig\Extension\CloudinaryExtension());

Usage
====

To display an image in your view simply use this tag

    {{ cl_image_tag("sample.jpg", {"alt" : "Sample Image" }) }}
    
To specify a specific image size you can use this tag

    {{ cl_image_tag("sample.jpg", { "width" : 100, "height" : 150, "crop" : "fill", "html_width" => 50, "html_height" => 75}) }}
                
You can add all options as described here 
http://cloudinary.com/documentation/php_image_manipulation


Tests
===

If you want to test this lib please add your cloudinary URL to phpunit.xml.dist
