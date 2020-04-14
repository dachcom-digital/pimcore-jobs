<?php

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;

class ItemTransformerDefinition implements ItemTransformerDefinitionInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $date;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $photoUrl;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $jobType;

    /**
     * @var string
     */
    protected $companyName;

    /**
     * @var string
     */
    protected $companyId;

    /**
     * @var string
     */
    protected $companyFullAddress;

    /**
     * @var string
     */
    protected $companyFacebookUrl;

    /**
     * @var string
     */
    protected $companyDataPolicyUrl;

    /**
     * @var string
     */
    protected $companyUrl;

    /**
     * @var string
     */
    protected $companyPageMatchingApproach;

    /**
     * @var string
     */
    protected $fullAddress;

    /**
     * @var string
     */
    protected $houseNumber;

    /**
     * @var string
     */
    protected $streetName;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $salary;

    /**
     * @var string
     */
    protected $salaryMin;

    /**
     * @var string
     */
    protected $salaryMax;

    /**
     * @var string
     */
    protected $salaryCurrency;

    /**
     * @var string
     */
    protected $salaryType;

    /**
     * @var array
     */
    protected $facebookApplyData;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @return string|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getPhotoUrl()
    {
        return $this->photoUrl;
    }

    /**
     * @param string $photoUrl
     */
    public function setPhotoUrl($photoUrl)
    {
        $this->photoUrl = $photoUrl;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string|null
     */
    public function getJobType()
    {
        return $this->jobType;
    }

    /**
     * @param string $jobType
     */
    public function setJobType($jobType)
    {
        $this->jobType = $jobType;
    }

    /**
     * @return string|null
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return string|null
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * @param string $companyId
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * @return string|null
     */
    public function getCompanyFullAddress()
    {
        return $this->companyFullAddress;
    }

    /**
     * @param string $companyFullAddress
     */
    public function setCompanyFullAddress($companyFullAddress)
    {
        $this->companyFullAddress = $companyFullAddress;
    }

    /**
     * @return string|null
     */
    public function getCompanyFacebookUrl()
    {
        return $this->companyFacebookUrl;
    }

    /**
     * @param string $companyFacebookUrl
     */
    public function setCompanyFacebookUrl($companyFacebookUrl)
    {
        $this->companyFacebookUrl = $companyFacebookUrl;
    }

    /**
     * @return string|null
     */
    public function getCompanyDataPolicyUrl()
    {
        return $this->companyDataPolicyUrl;
    }

    /**
     * @param string $companyDataPolicyUrl
     */
    public function setCompanyDataPolicyUrl($companyDataPolicyUrl)
    {
        $this->companyDataPolicyUrl = $companyDataPolicyUrl;
    }

    /**
     * @return string|null
     */
    public function getCompanyUrl()
    {
        return $this->companyUrl;
    }

    /**
     * @param string $companyUrl
     */
    public function setCompanyUrl($companyUrl)
    {
        $this->companyUrl = $companyUrl;
    }

    /**
     * @return string|null
     */
    public function getCompanyPageMatchingApproach()
    {
        return $this->companyPageMatchingApproach;
    }

    /**
     * @param string $companyPageMatchingApproach
     */
    public function setCompanyPageMatchingApproach($companyPageMatchingApproach)
    {
        $this->companyPageMatchingApproach = $companyPageMatchingApproach;
    }

    /**
     * @return string|null
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * @param string $fullAddress
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;
    }

    /**
     * @return string|null
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @param string $houseNumber
     */
    public function setHouseNumber($houseNumber)
    {
        $this->houseNumber = $houseNumber;
    }

    /**
     * @return string|null
     */
    public function getStreetName()
    {
        return $this->streetName;
    }

    /**
     * @param string $streetName
     */
    public function setStreetName($streetName)
    {
        $this->streetName = $streetName;
    }

    /**
     * @return string|null
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return string|null
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $region
     */
    public function setRegion($region)
    {
        $this->region = $region;
    }

    /**
     * @return string|null
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * @return string|null
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return string|null
     */
    public function getSalary()
    {
        return $this->salary;
    }

    /**
     * @param string $salary
     */
    public function setSalary($salary)
    {
        $this->salary = $salary;
    }

    /**
     * @return string|null
     */
    public function getSalaryMin()
    {
        return $this->salaryMin;
    }

    /**
     * @param string $salaryMin
     */
    public function setSalaryMin($salaryMin)
    {
        $this->salaryMin = $salaryMin;
    }

    /**
     * @return string|null
     */
    public function getSalaryMax()
    {
        return $this->salaryMax;
    }

    /**
     * @param string $salaryMax
     */
    public function setSalaryMax($salaryMax)
    {
        $this->salaryMax = $salaryMax;
    }

    /**
     * @return string|null
     */
    public function getSalaryCurrency()
    {
        return $this->salaryCurrency;
    }

    /**
     * @param string $salaryCurrency
     */
    public function setSalaryCurrency($salaryCurrency)
    {
        $this->salaryCurrency = $salaryCurrency;
    }

    /**
     * @return string|null
     */
    public function getSalaryType()
    {
        return $this->salaryType;
    }

    /**
     * @param string $salaryType
     */
    public function setSalaryType($salaryType)
    {
        $this->salaryType = $salaryType;
    }

    /**
     * @return array|null
     */
    public function getFacebookApplyData()
    {
        return $this->facebookApplyData;
    }

    /**
     * @param array $facebookApplyData
     *
     * Valid Nodes:
     *
     *   (string) application-callback-url
     *   (string) custom-questions-url
     *   (array) form-config
     *      (string) email-field
     *      (string) phone-number-field
     *      (string) work-experience-field
     */
    public function setFacebookApplyData($facebookApplyData)
    {
        $this->facebookApplyData = $facebookApplyData;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        try {
            $class = new \ReflectionClass(__CLASS__);
        } catch (\Exception $e) {
            return [];
        }

        foreach ($class->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            if (substr($method->getName(), 0, 3) === 'get') {

                $value = $method->invoke($this);

                if (empty($value)) {
                    continue;
                }

                $propName = preg_replace_callback('/([A-Z])/', function ($c) {
                    return '-' . strtolower($c[1]);
                }, lcfirst(substr($method->getName(), 3)));

                $result[$propName] = $method->invoke($this);
            }
        }

        return $result;
    }
}
