<?php


namespace App\Settings;


use Webmagic\Core\Entity\EntityRepo;

class SettingsRepo extends EntityRepo
{
    protected $entity = Settings::class;

    /**
     * Get signature
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getSignature()
    {
        $query = $this->query();
        $query = $query->where('email_signature', '!=', null)->select('email_signature');

        return $this->realGetOne($query);
    }

    /**
     * Get signature
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getSignatureImage()
    {
        $query = $this->query();
        $query = $query->where('email_signature_image', '!=', null)->select('email_signature_image');

        return $this->realGetOne($query);
    }


    /**
     * Get admin email
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getAdminEmail()
    {
        $query = $this->query();
        $query = $query->where('admin_email', '!=', null)->select('admin_email');

        return $this->realGetOne($query);
    }

}
