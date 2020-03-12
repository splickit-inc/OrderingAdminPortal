<?php namespace App\Http\Controllers\Menu;

use App\Model\MenuItem;
use App\Model\MenuType;
use App\Model\Menu;
use App\Model\Brand;
use \Request;
use \App\Service\Utility;
use \App\Http\Controllers\SplickitApiCurlController;



class MenuTypeController extends SplickitApiCurlController {

    //Load Sections and Mods to the UI
    public function index() {
        $menu_id = session('current_menu_id');

        $utility = new Utility();
        $lookups = ['cat_id','modifier_type'];
        $lookup_values = $utility->getLookupValues($lookups);

        $this->api_endpoint = 'menus/'.$menu_id;

        $menu_data = $this->makeCurlRequest();

        return ['lookup'=>$lookup_values, 'menu'=>$menu_data];
    }

    public function createMenuType() {
        $menu_id = session('current_menu_id');
        $utility = new Utility();



        $this->data = Request::all();
        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        if (substr(trim($this->data['items']), -1) == ';') {
            $this->data['items'] = rtrim($this->data['items'],';');
        }

        if (!isset($this->data['sizes'])) {
            $this->data['sizes'] = 'One Size';
        }

        if ($this->data['active_all_day']) {
            $this->data['start_time'] = '00:00:00';
            $this->data['end_time'] = '23:59:59';
        }
        else {
            $this->data['start_time'] = date("H:i", strtotime($this->data['start_time']." ".$this->data['start_time_am_pm']));
            $this->data['end_time'] = date("H:i", strtotime($this->data['end_time']." ".$this->data['end_time_am_pm']));
        }

        $this->api_endpoint = 'menus/'.$menu_id.'/menu_types';

        //Audit Data
        $this->audit_data['action'] = 'Create';
        $this->audit_data['auditable_type'] = 'Menu_Menu-Type';
        $this->audit_data['response_auditable_id'] = 'menu_type_id';

        $response = $this->makeCurlRequest(true);

        return $response;
    }

    public function getMenuType() {
        $this->api_endpoint = 'menus/102431/menu_types/48294?merchant_id=0';
        $response =  $this->makeCurlRequest(true);

        return $response;
    }

    public function updateMenuType() {
        $menu_id = session('current_menu_id');
        $utility = new Utility();

        $this->data = Request::all();

        $size_priority = 100;

        $this->data['active'] = $utility->convertBooleanYN($this->data['active']);

        foreach ($this->data['sizes'] as $index=>$size) {
            $this->data['sizes'][$index]['active'] = $utility->convertBooleanYN($size['active']);
            if (!isset($size['size_print_name'])) {
                $this->data['sizes'][$index]['size_print_name'] = $size['size_name'];
                $this->data['sizes'][$index]['priority'] = $size_priority + 100;
            }

            if (isset($size['default_selection'])) {
                if ($size['default_selection']) {
                    $this->data['sizes'][$index]['default_selection'] = '1';
                }
                else {
                    $this->data['sizes'][$index]['default_selection'] = '0';
                }
            }
            else {
                $this->data['sizes'][$index]['default_selection'] = '0';
            }
            if (isset($size['new'])) {
                if ($size['new']) {
                    if ($size['apply_all_items']) {
                        //$this->data['create_item_size_maps'] = true;
                    }
                    else {
                        $this->data['create_item_size_maps'] = false;
                    }
                }
            }
        }

        if ($this->data['active_all_day']) {
            $this->data['start_time'] = '00:00:00';
            $this->data['end_time'] = '23:59:59';
        }
        else {
            $this->data['start_time'] = date("H:i", strtotime($this->data['start_time']." ".$this->data['start_time_am_pm']));
            $this->data['end_time'] = date("H:i", strtotime($this->data['end_time']." ".$this->data['end_time_am_pm']));
        }

        $this->api_endpoint = 'menus/'.$menu_id.'/menu_types/'.$this->data['menu_type_id'].'?merchant_id=0';

        $this->setMethodPost();

        //Audit Data
        $this->audit_data['auditable_id'] = $this->data['menu_type_id'];
        $this->audit_data['action'] = 'Update';
        $this->audit_data['auditable_type'] = 'Menu_Menu-Type';

        $response =  $this->makeCurlRequest(true);

        if (($response['start_time'] == '00:00:00' || $response['start_time'] == '12:00') && $response['end_time'] == '23:59:59') {
            $response['active_all_day'] = true;
            $response['start_time'] = '00:00:00';
        }
        else {
            $response['active_all_day'] = false;
        }

        $response['start_time_am_pm'] = date('a', strtotime($response['start_time']));
        $response['start_time'] = date('h:i', strtotime($response['start_time']));

        $response['end_time_am_pm'] = date('a', strtotime($response['end_time']));
        $response['end_time'] = date('h:i', strtotime($response['end_time']));

        foreach ($response['sizes'] as $size_index=>$size) {
            $response['sizes'][$size_index]['active'] = $utility->convertYNBoolean($size['active']);
            $response['sizes'][$size_index]['default_selection'] = $utility->converOneZeroNBoolean($size['default_selection']);
        }
        $response['active'] = $utility->convertYNBoolean($response['active']);

        $response['menu_items'] = $response['items'];
        unset($response['items']);

        return $response;
    }

    public function updateMenuTypePriorities() {
        $this->data = Request::all();
        $current_priority = count($this->data['menu_types']) * 1000;

        foreach ($this->data['menu_types'] as $menu_type) {
            $menu_type = MenuType::find($menu_type['menu_type_id']);

            $menu_type->priority = $current_priority;

            $menu_type->save();

            $menu_type_items = MenuItem::where('menu_type_id', '=', $menu_type['menu_type_id'])->orderBy('priority')->get();

            $item_count = 1;

            foreach ($menu_type_items as $menu_type_item) {
                $item_priority = MenuItem::find($menu_type_item->item_id);
                $item_priority->priority = $current_priority + $item_count;
                $item_priority->save();
                $item_count++;
            }

            $current_priority = $current_priority - 1000;
        }
        return 1;
    }

    public function currentMenuTypes() {
        return session('current_menu_types');
    }

    public function deleteMenuType($menu_type) {
        $menu_id = session('current_menu_id');

        $this->api_endpoint = 'menus/'.$menu_id.'/menu_types/'.$menu_type;

        $this->setMethodDelete();

        $response = $this->makeCurlRequest();

        return $response;
    }

    public function imageUpload() {
        $data = Request::all();

        $menu_id = session('current_menu_id');

        $menu = Menu::find($menu_id);
        $brand = Brand::find($menu->brand_id);


        $file = $data['file'];
        //https://cloudinary.com/blog/image_optimization_in_php
        //Configuration
        //Main Image

        $extension = strtolower(substr($file->getMimeType(), strrpos($file->getMimeType(), '/') + 1));

        if ($extension == 'png') {
            $original_image = imagecreatefrompng($file);
        }
        elseif ($extension == 'jpg' || $extension == 'jpeg') {
            $original_image = imagecreatefromjpeg($file);
        }
        elseif ($extension == 'gif') {
            $original_image = imagecreatefromgif($file);
        }

        $destination_url = public_path().'/tmp/menu_type_image/'.$data['menu_type_id'].'.jpg';

        imagejpeg($original_image, $destination_url);

        $image_data = getimagesize($destination_url);

        $result_image_width = 1300;
        $result_image_height = 375;

        $result_image = imagecreatetruecolor($result_image_width, $result_image_height);

        imagealphablending($result_image, false);

        imagesavealpha($result_image, true);

        imagecopyresampled($result_image, $original_image, 0, 0, 0, 0, $result_image_width, $result_image_height, $image_data[0], $image_data[1]);

        imagejpeg($result_image, $destination_url);
        // Create new imagick object
        $imagic_image = new \Imagick($destination_url);
        // Optimize the image layers
        $imagic_image->optimizeImageLayers();
        // Compression and quality
        $imagic_image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagic_image->setImageCompressionQuality(25);
        // Write the image back
        $imagic_image->writeImages($destination_url, true);

        $full_file_name = $brand->brand_external_identifier.'/menu-section-images/'.$data['menu_type_id'].'_'.time().'.jpg';

        $s3 = \AWS::createClient('s3');

        $oldMenuTypeRecord = MenuType::find($data['menu_type_id']);

        if (strlen($oldMenuTypeRecord->image_url) > 3) {
            $key = str_replace('https://d38o1hjtj2mzwt.cloudfront.net/', '', $oldMenuTypeRecord->image_url);

            $response = $s3->deleteObject(array(
                'Bucket'     => 'com.yourbiz.products',
                'Key'        => $key,
                'ACL'          => 'public-read',
                'SourceFile' => $destination_url,
            ));
        }


        $response = $s3->putObject(array(
            'Bucket'     => 'com.yourbiz.products',
            'Key'        => $full_file_name,
            'ACL'          => 'public-read',
            'SourceFile' => $destination_url,
        ));

        $menuType = MenuType::find($data['menu_type_id']);
        $menuType->image_url = str_replace('https://s3.amazonaws.com/com.yourbiz.products/', 'https://d38o1hjtj2mzwt.cloudfront.net/', $response['ObjectURL']);
        $menuType->save();

        unlink($destination_url);

        return [
            'exist' => true,
            'image' => $menuType->image_url."?".time()
        ];
//        try {
//            $data = Request::all();
//            $web_skin_mapping = new WebSkinMapping();
//            $image_mapping_configs = $web_skin_mapping->image_mapping['hero_image_url'];
//
//            if (!empty($data['file'])) {
//                /** @var UploadedFile $mainFile */
//                $file = $data['file'];
//
//                //https://cloudinary.com/blog/image_optimization_in_php
//
//                //Configuration
//
//
//                //Main Image
//                $extension = strtolower(substr($file->getMimeType(), strrpos($file->getMimeType(), '/') + 1));
//
//                if ($extension == 'png') {
//                    $original_image = imagecreatefrompng($file);
//                }
//                elseif ($extension == 'jpg' || $extension == 'jpeg') {
//                    $original_image = imagecreatefromjpeg($file);
//                }
//                elseif ($extension == 'gif') {
//                    $original_image = imagecreatefromgif($file);
//                }
//
//                $destination_url = public_path().'/tmp/hero_image/'.session('current_brand_external_identifier').'.jpg';
//                imagejpeg($original_image, $destination_url);
//
//                $data = getimagesize($destination_url);
//
//                $result_image_width = 1500;
//                $result_image_height = 335;
//
//                $result_image = imagecreatetruecolor($result_image_width, $result_image_height);
//
//                imagealphablending($result_image, false);
//                imagesavealpha($result_image, true);
//
//                imagecopyresampled($result_image, $original_image, 0, 0, 0, 0, $result_image_width, $result_image_height, $data[0], $data[1]);
//
//                imagejpeg($result_image, $destination_url);
//
//                // Create new imagick object
//                $imagic_image = new \Imagick($destination_url);
//
//                // Optimize the image layers
//                $imagic_image->optimizeImageLayers();
//
//                // Compression and quality
//                $imagic_image->setImageCompression(\Imagick::COMPRESSION_JPEG);
//                $imagic_image->setImageCompressionQuality(25);
//
//                // Write the image back
//                $imagic_image->writeImages($destination_url, true);
//
//                $full_file_name = $image_mapping_configs['s3_path'].'hero_image_'.session('current_web_skin_id').'.jpg';
//
//                $s3 = \AWS::createClient('s3');
//
//                $response = $s3->putObject(array(
//                    'Bucket'     => 'com.yourbiz.products',
//                    'Key'        => $full_file_name,
//                    'ACL'          => 'public-read',
//                    'SourceFile' => $destination_url,
//                ));
//
//                $skin_config = SkinConfig::find(session('current_web_skin_id'));
//                $skin_config->hero_image_url = $response['ObjectURL'];
//                $skin_config->save();
//
//                unlink($destination_url);
//
//                return [
//                    'exist' => true,
//                    'image' => $skin_config->hero_image_url."?".time()
//                ];
//            }
//            return response()->json(['error' => "The request must have the files content."], 404);
//        } catch (\Exception $exception) {
//            return response()->json(['error' => $exception->getMessage()], 404);
//        }
    }

}
