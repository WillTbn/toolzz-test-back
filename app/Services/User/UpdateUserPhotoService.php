<?php

namespace App\Services\User;

use App\Models\Customer;
use App\Models\User;
use App\Models\UsersCustomer;
use App\Services\Service;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Vinkla\Hashids\Facades\Hashids;

/**
 * User Customer
 *
 */
class UpdateUserPhotoService extends Service
{
     /**
     * User
     *
     * @var User
     */
    private User $user;

    /**
     * photo
     *
     * @var UploadedFile $photo
     */
    private  UploadedFile $photo;

    /**
     * name file
     * @var string $fileName
     */
    private string $fileName;
    /**
     * name path
     * @var string $path
     */
    private string $path;
    /**
     * user new
     * @var User $userNew
     */
    private User $userNew;
    protected $default = 'default.jpg';
    /**
     * Service constructor
     *
     */
    public function __construct(
        User $user,
        UploadedFile $photo
    )
    {
       $this->user= $user;
       $this->photo = $photo;
    }
    /**
     * seta name file to photo user
     */
    public function setFileName()
    {
        $this->fileName = $this->photo->hashName();
    }

    public function getFileName()
    {
        return $this->fileName;
    }
    /**
     * set local path image photo user
     */
    public function setPath()
    {
        $this->path = $this->photo->storePublicly('photos/'.$this->user->id, 'public', $this->getFileName());
    }
    /**
     * set new data user
     */
    public function setUserNew(User $userNew){
        $this->userNew =$userNew;
    }
    /**
     * clear url complet and delete old photo user
     */
    public function deleteOldPhoto()
    {
        $url = parse_url($this->user->photo);
        $path = ltrim($url['path'], '/storage\/');
        if(Storage::disk('public')->exists($path) && $path != $this->default){
            Storage::disk('public')->delete($path);
        }
    }
    public function getPath()
    {
        return $this->path;
    }
    /**
     * get data user new
     */
    public function getUserNew():User
    {
        return $this->userNew;
    }
    /**
     * Execute service logic
     *
     * @return CreateUserCustomerService
     */
    public function execute(): UpdateUserPhotoService
    {
        $this->setFileName();
        $this->setPath();
        $this->deleteOldPhoto();
        $this->user->photo = $this->getPath();
        $this->user->updateOrFail();
        $this->setUserNew($this->user);

        return $this;
    }
}
