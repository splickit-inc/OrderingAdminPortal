<?php
/**
 * Created by PhpStorm.
 * User: diego.rodriguez
 * Date: 10/12/17
 * Time: 10:55 AM
 */

namespace App\Service;

use App\Model\Lead;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\UploadedFile;

class S3FileService extends BaseService {
    const BUCKET = 'com.yourbiz.products';
    const ASSETS = '/web/brand-assets/';

    /**
     * @param Lead           $lead
     * @param UploadedFile[] $files
     * @return array|bool
     */
    public function uploadMenuFiles(Lead $lead, array $files) {
        $this->clearErrors();

        if (is_null($lead['merchant_id']) || is_null($lead['first_form_completion'])) {
            $this->addError("The customer wasn't registered properly, please fill form 1 first.");
            return false;
        }
        if (!is_null($lead['second_form_completion'])) {
            $this->addError('Menu files were already uploaded for this customer!');
            return false;
        }

        $identifier = 'com.yourbiz.' . $lead['subdomain'];
        $dir = $identifier . self::ASSETS;
        try {
            $this->clearPath($dir);
            $s3 = \AWS::createClient('s3');
            $res = [];
            /** @var $file UploadedFile */
            foreach ($files as $file) {
                $result = $s3->putObject([
                    'Bucket' => self::BUCKET,
                    'Key' => $dir . $this->uniqueFilename($file->getClientOriginalExtension()),
                    'ACL' => 'public-read',
                    'SourceFile' => $file
                ]);
                array_push($res, $result->get('ObjectURL'));
            }
            return $res;
        } catch (S3Exception $e) {
            $this->addError($e->getMessage());
        }
        return false;
    }

    private function clearPath($path) {
        $s3 = \AWS::createClient('s3');
        $s3->deleteMatchingObjects(self::BUCKET, $path);
    }

    private function uniqueFilename($extension) {
        return Uuid::uuid4()->toString() . '.' . $extension;
    }
}