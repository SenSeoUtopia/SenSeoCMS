<?php
use Intervention\Image\ImageManagerStatic as Image;

class ImageController extends Controller
{



    /* Avatar Upload */
    public function avatar($f3)
    {

        $data = $f3->get("POST");

        $home_url = $this->home_url;

        $upload_dir = $this->upload_dir;

        $get_user = $f3->get("SESSION.user");

        $user_id = $get_user->id;

        $image_url = $data['imgUrl'];

        // resized sizes
        $imgW = $data['imgW'];
        $imgH = $data['imgH'];
        // offsets
        $imgY1 = $data['imgY1'];
        $imgX1 = $data['imgX1'];
        // crop box
        $cropW = $data['cropW'];
        $cropH = $data['cropH'];
        // rotation angle
        $angle = $data['rotation'];

        $image_info = getImageSize($image_url);
        switch ($image_info['mime']) {
            case 'image/gif':
                $extension = 'gif';
                break;
            case 'image/jpeg':
                $extension = 'jpg';
                break;
            case 'image/png':
                $extension = 'png';
                break;
            default:
                $extension = 'jpg';
                break;
        }

        $file_name = "avatar";
        $file_name_thumb = "thumb_avatar";

        if (!file_exists("$upload_dir/$user_id")) mkdir("$upload_dir/$user_id");
        if (!file_exists("$upload_dir/$user_id/profile_pics")) mkdir("$upload_dir/$user_id/profile_pics");

        $destination = "$upload_dir/$user_id/profile_pics/$file_name.$extension";
        $destination_thumb = "$upload_dir/$user_id/profile_pics/$file_name_thumb.$extension";

        $avatar_url = "$home_url/uploads/$user_id/profile_pics/$file_name.$extension";

        $image = Image::make($image_url)->resize($imgW, $imgH)->crop($cropW, $cropH, $imgX1, $imgY1)->save($destination);
        $image = Image::make($destination)->resize(30, 30)->save($destination_thumb);

        $user = User::find($user_id);

        $user->avatar = $avatar_url;

        $user->save();

        if (!$image) {

            return Response::json(['status' => 'error', 'message' => 'Server error while uploading'], 200);

        }

        return Response::json(['status' => 'success', 'url' => $avatar_url . '?' . time()], 200);
    }

}