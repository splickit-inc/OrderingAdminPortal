<?php namespace App\Http\Controllers;

use Request;
use \DB;
use \Cache;
use \App\Model\Brand;
use \App\Model\Skin;
use \App\Model\SkinConfig;
use \App\Model\DefaultSkinConfig;
use \App\Service\Image;
use \App\Service\WebSkinMapping;
use \App\Service\Utility;
use Illuminate\Support\Facades\Auth;
use App\Service\SplickitWebSkinningCurlService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as ImageManager;

use \App\Http\Controllers\SplickitApiCurlController;

class WebSkinController extends SplickitApiCurlController {

    public function load() {
        $brand = new Brand();
        $brands = $brand->allBrands();


        $recently_visited_web_skins = $this->getRecentlyVisited();

        return [
            'brands'=>$brands,
            'recently_visited_web_skins'=>$recently_visited_web_skins
        ];
    }

    public function index() {
        $current_web_skin_id = session('current_web_skin_id');

        $web_skin_mapping = new WebSkinMapping();

        $full_config = $web_skin_mapping->full_mapping;

        $return_fields = [];
        $response_fields = [];

        foreach ($full_config as $config) {
            $return_fields[] = $config['field_name'];
            $response_fields[$config['field_name']] = [
              'name'=> $config['name'],
              'child_color_fields'=> $config['child_color_fields'],
              'number'=> $config['item_number']
            ];
        }

        $web_skin_values = SkinConfig::where('id', '=', $current_web_skin_id)->get($return_fields)->toArray()[0];

        foreach ($web_skin_values as $index=>$value) {
            $full_config[$index]['value'] = $value;
            $full_config[$index]['hover'] = false;
        }

        $web_skin_values = SkinConfig::where('id', '=', $current_web_skin_id)->get(['custom_css'])->toArray()[0];

        $full_config['hero_image_url']['value'] = $full_config['hero_image_url']['value']."?".time();
        $full_config['primary_logo_url']['value'] = $full_config['primary_logo_url']['value']."?".time();
        $full_config['custom_css']['value'] = $web_skin_values['custom_css'];



        return $full_config;
    }

    public function create() {
        $data = Request::all();

        $this->data = $data;

        $web_skin = Skin::where('brand_id', '=', session('current_brand_id'))->first();

        $web_skin_config = new SkinConfig();

        $web_skin_config->brand_id = session('current_brand_id');

        $web_skin_config->skin_id = $web_skin->skin_id;

        $web_skin_config->name = $data['name'];

        $web_skin_config->save();

        session(['current_web_skin_id'=> $web_skin_config->id]);

        return 1;
    }

    public function getDefaultSkins() {

        $template_skins = new DefaultSkinConfig();
        $templates = $template_skins->getDefaultSkins();

        return $templates;
    }

    public function createSkinTemplate(){

        $data = Request::all();
        $this->data = $data;

        $web_skin = Skin::where('brand_id', '=', $data['brand'])->first();

        $web_skin_config = new SkinConfig();

        $web_skin_config->brand_id = $data['brand'];

        $web_skin_config->skin_id = $web_skin->skin_id;

        $web_skin_config->name = $data['skin_name'];
        $web_skin_config->bag_bg_color = $data['template']['bag_bg_color'];
        $web_skin_config->bag_text_color = $data['template'] ['bag_text_color'];
        $web_skin_config->card_bg_color = $data['template'] ['card_bg_color'];
        $web_skin_config->card_border_color = $data['template'] ['card_border_color'];
        $web_skin_config->card_separator_color = $data['template'] ['card_separator_color'];
        $web_skin_config->check_color = $data['template'] ['check_color'];
        $web_skin_config->checkbox_border_color = $data['template'] ['checkbox_border_color'];
        $web_skin_config->checkbox_checked_color = $data['template'] ['checkbox_checked_color'];
        $web_skin_config->checkbox_unchecked_color = $data['template'] ['checkbox_unchecked_color'];
        $web_skin_config->dark_text_color = $data['template'] ['dark_text_color'];
        $web_skin_config->dialog_bg_color = $data['template'] ['dialog_bg_color'];
        $web_skin_config->drop_down_border_color = $data['template'] ['drop_down_border_color'];
        $web_skin_config->footer_bg_color = $data['template'] ['footer_bg_color'];
        $web_skin_config->footer_top_separator_color = $data['template']['footer_top_separator_color'];
        $web_skin_config->info_window_dark_text_color = $data['template']['info_window_dark_text_color'];
        $web_skin_config->info_window_light_text_color = $data['template']['info_window_light_text_color'];
        $web_skin_config->light_text_color = $data['template'] ['light_text_color'];
        $web_skin_config->link_color = $data['template'] ['link_color'];
        $web_skin_config->link_hover_color = $data['template']['link_hover_color'];
        $web_skin_config->loyalty_border_color = $data['template'] ['loyalty_border_color'];
        $web_skin_config->loyalty_nth_row_color = $data['template'] ['loyalty_nth_row_color'];
        $web_skin_config->medium_text_color = $data['template'] ['medium_text_color'];
        $web_skin_config->menu_item_hover_color = $data['template'] ['menu_item_hover_color'];
        $web_skin_config->menu_nav_bg_color = $data['template']['menu_nav_bg_color'];
        $web_skin_config->menu_nav_item_hover_color = $data['template']['menu_nav_item_hover_color'];
        $web_skin_config->menu_nav_item_selected_text_color = $data['template']['menu_nav_item_selected_text_color'];
        $web_skin_config->menu_nav_item_text_color = $data['template'] ['menu_nav_item_text_color'];
        $web_skin_config->menu_nav_primary_item_text_color = $data['template']['menu_nav_primary_item_text_color'];
        $web_skin_config->menu_price_text_color = $data['template'] ['menu_price_text_color'];
        $web_skin_config->modal_header_footer_color = $data['template']['modal_header_footer_color'];
        $web_skin_config->modal_row_color = $data['template'] ['modal_row_color'];
        $web_skin_config->modal_row_text_color = $data['template'] ['modal_row_text_color'];
        $web_skin_config->modal_separator_color = $data['template'] ['modal_separator_color'];
        $web_skin_config->plus_minus_button_bg_active = $data['template']['plus_minus_button_bg_active'];
        $web_skin_config->plus_minus_button_bg_inactive = $data['template']['plus_minus_button_bg_inactive'];
        $web_skin_config->plus_minus_button_border_color = $data['template']['plus_minus_button_border_color'];
        $web_skin_config->primary_bg_color = $data['template'] ['primary_bg_color'];
        $web_skin_config->primary_button_color = $data['template'] ['primary_button_color'];
        $web_skin_config->primary_button_hover_color = $data['template']['primary_button_hover_color'];
        $web_skin_config->primary_button_text_color = $data['template']['primary_button_text_color'];
        $web_skin_config->secondary_button_color = $data['template'] ['secondary_button_color'];
        $web_skin_config->secondary_button_hover_color = $data['template']['secondary_button_hover_color'];
        $web_skin_config->secondary_button_text_color = $data['template']['secondary_button_text_color'];
        $web_skin_config->separator_color = $data['template'] ['separator_color'];
        $web_skin_config->small_nav_bg_color = $data['template'] ['small_nav_bg_color'];
        $web_skin_config->small_nav_separator_color = $data['template']['small_nav_separator_color'];
        $web_skin_config->small_nav_text_color = $data['template'] ['small_nav_text_color'];
        $web_skin_config->small_primary_nav_separator_color = $data['template']['small_primary_nav_separator_color'];
        $web_skin_config->small_secondary_nav_separator_color = $data['template']['small_secondary_nav_separator_color'];
        $web_skin_config->text_field_border_color = $data['template'] ['text_field_border_color'];
        $web_skin_config->address_block_text_color = $data['template'] ['address_block_text_color'];
        $web_skin_config->app_logo_url = $data['template'] ['app_logo_url'];
        $web_skin_config->error_logo_url = $data['template'] ['error_logo_url'];
        $web_skin_config->hero_image_url = $data['template'] ['hero_image_url'];
        $web_skin_config->loyalty_logo_url = $data['template'] ['loyalty_logo_url'];
        $web_skin_config->primary_logo_url = $data['template'] ['primary_logo_url'];
        $web_skin_config->custom_font_url = $data['template'] ['custom_font_url'];
        $web_skin_config->name_custom_font_for_menu = $data['template']['name_custom_font_for_menu'];
        $web_skin_config->loyalty_behind_card_bg = $data['template'] ['loyalty_behind_card_bg'];
        $web_skin_config->override_css = $data['template'] ['override_css'];
        $web_skin_config->map_pin_color = $data['template'] ['map_pin_color'];

        $web_skin_config->save();

        session(['current_web_skin_id'=> $web_skin_config->id]);

        return 1;
    }


    public function saveColor() {
        $data = Request::all();

        $web_skin = SkinConfig::find(session('current_web_skin_id'));

        $web_skin->{$data['field_name']} = $data['value'];

        $web_skin_mapping = new WebSkinMapping();

        if (isset($web_skin_mapping->full_mapping[$data['field_name']])) {

            //Child Colors that have the exact same color
            if ($web_skin_mapping->full_mapping[$data['field_name']]['child_color_fields']) {
                foreach($web_skin_mapping->full_mapping[$data['field_name']]['child_color_fields'] as $child_field) {
                    $web_skin->{$child_field} = $data['value'];
                }
            }

            //Handle Off Color Fields
            if (isset($web_skin_mapping->full_mapping[$data['field_name']]['child_off_color_fields'])) {
                foreach($web_skin_mapping->full_mapping[$data['field_name']]['child_off_color_fields'] as $child_field) {
                    $web_skin->{$child_field} = $data['off_color'];
                }
            }

            //Handle Light Dark Colors
            if (isset($web_skin_mapping->full_mapping[$data['field_name']]['child_light_dark_fields'])) {
                foreach($web_skin_mapping->full_mapping[$data['field_name']]['child_light_dark_fields'] as $child_field) {
                    $web_skin->{$child_field} = $data['light_dark'];
                }
            }
        }
        $web_skin->save();
        return 1;
    }

    public function saveCustomCSS() {
        $data = Request::all();

        $web_skin = SkinConfig::find(session('current_web_skin_id'));
        $web_skin->custom_css = $data['custom_css'];

        $web_skin->save();
    }

    public function addSkinToRecentlyVisited($web_skin, $brand) {
        $new_web_skin = [
            'web_skin_id' => $web_skin->id,
            'brand_name' => $brand->brand_name,
            'brand_id' => $brand->brand_id
        ];

        if (Cache::has('recently_visited_web_skins'))
        {
            $recently_visited_web_skins = Cache::get('recently_visited_web_skins');

            //Remove Merchant if It's Already in Recently Visited
            foreach ($recently_visited_web_skins as $key => $web_skin) {
                if($web_skin['web_skin_id'] == $new_web_skin['web_skin_id']) {
                    unset($recently_visited_web_skins[$key]);
                    break;
                }
            }

            //Remove the Oldest Recently Visited if We're at the Maximum of 5
            if (count($recently_visited_web_skins) == 5) {
                array_pop($recently_visited_web_skins);
            }

            //Add the New Menu to the Beginning of the Array
            array_unshift($recently_visited_web_skins, $new_web_skin);

            Cache::put('recently_visited_web_skins', $recently_visited_web_skins, 10080);
        }
        else {
            $recently_visited_web_skins = [];
            $recently_visited_web_skins[] = $new_web_skin;

            Cache::put('recently_visited_web_skins', $recently_visited_web_skins, 10080);
        }
    }

    public function getRecentlyVisited() {
        if (Cache::has('recently_visited_web_skins'))
        {
            return Cache::get('recently_visited_web_skins');
        }
        else {
            return [];
        }
    }

    public function pushSkinToPreview() {
        $web_skin = SkinConfig::find(session('current_web_skin_id'))->toArray();

        unset($web_skin['override_css']);
        unset($web_skin['id']);
        unset($web_skin['name']);
        unset($web_skin['brand_id']);
        unset($web_skin['created_at']);
        unset($web_skin['updated_at']);

        unset($web_skin['name_custom_font_for_menu']);

        $web_skin['with_images'] = "true";
        $web_skin['with_map_pins'] = "true";

        $web_skin = array_map(function($field) {
            return trim($field);
        }, $web_skin);

        $brand = Brand::where('brand_id', '=', session('current_brand_id'))->first();

        $brand_identifier = str_replace('com.yourbiz.', '', $brand->brand_external_identifier);

        $curl_service = new SplickitWebSkinningCurlService();

        $authorization_headers = ['Authorization: key='.env('WEB_SKIN_SERVICE_AUTH_TOKEN')];

        $url = env('SPLICKIT_WEBSKIN_PREVIEW_URL').$brand_identifier;

        $response = $curl_service->makeRequest($url, $web_skin, $authorization_headers);
        Log::info($authorization_headers);
        Log::info($url);
        Log::info("Skin");
        Log::info($web_skin);
        Log::info("Response from Skinning Service");
        Log::info($response);
        session(['web_skin_publish_token'=> $response['skin']['publishing_token']]);

        return $response;
    }

    public function setBrand($brand_id) {
        $brand = Brand::where('brand_id', '=', $brand_id)->first();
        session(['current_brand_external_identifier'=> $brand->brand_external_identifier]);
        session(['current_brand_id'=> $brand->brand_id]);
    }

    public function getBrandSkins($brand_id) {
        $this->setBrand($brand_id);

        $web_skin = new SkinConfig();

        return $web_skin->getBrandSkinsGlobal($brand_id);
    }

    public function setSkin($skin_id) {
        session(['current_web_skin_id'=> $skin_id]);
        return 1;
    }

    public function heroImageUpload() {
            $data = Request::all();
            $web_skin_mapping = new WebSkinMapping();
            $image_mapping_configs = $web_skin_mapping->image_mapping['hero_image_url'];

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

        $destination_url = public_path().'/tmp/hero_image/'.session('current_brand_external_identifier').'.jpg';
        imagejpeg($original_image, $destination_url);

        $data = getimagesize($destination_url);

        $result_image_width = 1500;
        $result_image_height = 335;

        $result_image = imagecreatetruecolor($result_image_width, $result_image_height);

        imagealphablending($result_image, false);
        imagesavealpha($result_image, true);

        imagecopyresampled($result_image, $original_image, 0, 0, 0, 0, $result_image_width, $result_image_height, $data[0], $data[1]);

        imagejpeg($result_image, $destination_url);

        // Create new imagick object
        $imagic_image = new \Imagick($destination_url);

        // Optimize the image layers
        $imagic_image->optimizeImageLayers();

        // Compression and quality
        $imagic_image->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $imagic_image->setImageCompressionQuality(100);

        // Write the image back
        $imagic_image->writeImages($destination_url, true);

        $full_file_name = $image_mapping_configs['s3_path'].'hero_image_'.session('current_web_skin_id').'.jpg';

        $s3 = \AWS::createClient('s3');

        $response = $s3->putObject(array(
            'Bucket'     => 'com.yourbiz.products',
            'Key'        => $full_file_name,
            'ACL'          => 'public-read',
            'SourceFile' => $destination_url,
        ));

        $skin_config = SkinConfig::find(session('current_web_skin_id'));
        $skin_config->hero_image_url = $response['ObjectURL'];
        $skin_config->save();

        unlink($destination_url);

        return [
            'exist' => true,
            'image' => $skin_config->hero_image_url."?".time()
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

    public function logoImageUpload() {
        $data = Request::all();

        $web_skin_mapping = new WebSkinMapping();
        $image_mapping_configs = $web_skin_mapping->image_mapping[$data['image_field']];

        $user = Auth::user();

        $file = $_FILES["key"]["tmp_name"];

        $utility = new Utility();

        $extension = $utility->getImageExtension($_FILES["key"]['name']);

        $file_name = $user->id."-".session('current_web_skin_id').".".$extension;

        $tmp_file_location = public_path()."/img/web_skin/logo/".$file_name;

        move_uploaded_file($file, $tmp_file_location);

        $image = new Image($tmp_file_location);

        $image->getFileImageWidthHeight();
        $image->newWidthHeight($image_mapping_configs);

        $tmp_image_location = public_path().$image_mapping_configs['tmp_location'].$user->id."-".
                              session('current_web_skin_id').".".$image_mapping_configs['new_file_extension'];

        $image->createImageObject($tmp_image_location);

        $key = session('current_brand_external_identifier').$image_mapping_configs['s3_path']."Logo_".session('current_web_skin_id').".".$extension;

        $s3 = \AWS::createClient('s3');

        $response = $s3->putObject(array(
            'Bucket'     => 'com.yourbiz.products',
            'Key'        => $key,
            'ACL'          => 'public-read',
            'SourceFile' => $tmp_image_location,
        ));

//        $cloudFrontClient = new \Aws\CloudFront\CloudFrontClient();
//
//        $cloudFrontClient->create

        $web_skin = SkinConfig::find(session('current_web_skin_id'));

        $web_skin->{$data['image_field']} = $response['ObjectURL'];

        $web_skin_mapping = new WebSkinMapping();

        $full_config = $web_skin_mapping->full_mapping;

        $image_child_fields = $full_config[$data['image_field']]['child_image_fields'];

        foreach ($image_child_fields as $field) {
            $web_skin->{$field} = $response['ObjectURL'];
        }

        $web_skin->save();

        return [
            'image_url'=>$response['ObjectURL']
        ];
    }

    protected function markSkinAsProductionSkin($web_skin) {
        SkinConfig::where('skin_id', '=', $web_skin['skin_id'])
            ->update(['in_production' => 0]);

        $productionSkin = SkinConfig::find($web_skin['id']);

        $productionSkin->in_production = 1;

        $productionSkin->save();
    }

    public function publishSkinToProduction() {
        $web_skin = SkinConfig::find(session('current_web_skin_id'))->toArray();

        unset($web_skin['override_css']);
        unset($web_skin['id']);
        unset($web_skin['name']);
        unset($web_skin['brand_id']);
        unset($web_skin['created_at']);
        unset($web_skin['updated_at']);

        unset($web_skin['name_custom_font_for_menu']);

        $web_skin['with_images'] = "true";
        $web_skin['with_map_pins'] = "true";

        $web_skin = array_map(function($field) {
            return trim($field);
        }, $web_skin);

        $brand = Brand::where('brand_id', '=', session('current_brand_id'))->first();

        $brand_identifier = str_replace('com.yourbiz.', '', $brand->brand_external_identifier);

        $authorization_headers = ['Authorization: key='.env('WEB_SKIN_SERVICE_AUTH_TOKEN'), 'Publishing-Token: '.session('web_skin_publish_token')];

        $url = env('SPLICKIT_WEBSKIN_PUBLISH_URL').$brand_identifier;

        $curl_service = new SplickitWebSkinningCurlService();

        $response = $curl_service->makeRequest($url, $web_skin, $authorization_headers);

        $web_skin = SkinConfig::find(session('current_web_skin_id'))->toArray();

        $this->markSkinAsProductionSkin($web_skin);

        return $response;
    }
}