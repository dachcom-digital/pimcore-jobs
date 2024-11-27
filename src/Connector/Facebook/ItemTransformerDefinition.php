<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace JobsBundle\Connector\Facebook;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;

class ItemTransformerDefinition implements ItemTransformerDefinitionInterface
{
    protected ?string $title = null;
    protected ?string $date = null;
    protected ?string $id = null;
    protected ?string $photoUrl = null;
    protected ?string $description = null;
    protected ?string $jobType = null;
    protected ?string $companyName = null;
    protected ?string $companyId = null;
    protected ?string $companyFullAddress = null;
    protected ?string $companyFacebookUrl = null;
    protected ?string $companyDataPolicyUrl = null;
    protected ?string $companyUrl = null;
    protected ?string $companyPageMatchingApproach = null;
    protected ?string $fullAddress = null;
    protected ?string $houseNumber = null;
    protected ?string $streetName = null;
    protected ?string $city = null;
    protected ?string $region = null;
    protected ?string $country = null;
    protected ?string $postalCode = null;
    protected ?string $salary = null;
    protected ?string $salaryMin = null;
    protected ?string $salaryMax = null;
    protected ?string $salaryCurrency = null;
    protected ?string $salaryType = null;
    protected ?array $facebookApplyData = null;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(string $photoUrl): void
    {
        $this->photoUrl = $photoUrl;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getJobType(): ?string
    {
        return $this->jobType;
    }

    public function setJobType(string $jobType): void
    {
        $this->jobType = $jobType;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getCompanyId(): ?string
    {
        return $this->companyId;
    }

    public function setCompanyId(string $companyId): void
    {
        $this->companyId = $companyId;
    }

    public function getCompanyFullAddress(): ?string
    {
        return $this->companyFullAddress;
    }

    public function setCompanyFullAddress(string $companyFullAddress): void
    {
        $this->companyFullAddress = $companyFullAddress;
    }

    public function getCompanyFacebookUrl(): ?string
    {
        return $this->companyFacebookUrl;
    }

    public function setCompanyFacebookUrl(string $companyFacebookUrl): void
    {
        $this->companyFacebookUrl = $companyFacebookUrl;
    }

    public function getCompanyDataPolicyUrl(): ?string
    {
        return $this->companyDataPolicyUrl;
    }

    public function setCompanyDataPolicyUrl(string $companyDataPolicyUrl): void
    {
        $this->companyDataPolicyUrl = $companyDataPolicyUrl;
    }

    public function getCompanyUrl(): ?string
    {
        return $this->companyUrl;
    }

    public function setCompanyUrl(string $companyUrl): void
    {
        $this->companyUrl = $companyUrl;
    }

    public function getCompanyPageMatchingApproach(): ?string
    {
        return $this->companyPageMatchingApproach;
    }

    public function setCompanyPageMatchingApproach(string $companyPageMatchingApproach): void
    {
        $this->companyPageMatchingApproach = $companyPageMatchingApproach;
    }

    public function getFullAddress(): ?string
    {
        return $this->fullAddress;
    }

    public function setFullAddress(string $fullAddress): void
    {
        $this->fullAddress = $fullAddress;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(string $houseNumber): void
    {
        $this->houseNumber = $houseNumber;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): void
    {
        $this->streetName = $streetName;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): void
    {
        $this->salary = $salary;
    }

    public function getSalaryMin(): ?string
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(string $salaryMin): void
    {
        $this->salaryMin = $salaryMin;
    }

    public function getSalaryMax(): ?string
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(string $salaryMax): void
    {
        $this->salaryMax = $salaryMax;
    }

    public function getSalaryCurrency(): ?string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(string $salaryCurrency): void
    {
        $this->salaryCurrency = $salaryCurrency;
    }

    public function getSalaryType(): ?string
    {
        return $this->salaryType;
    }

    public function setSalaryType(string $salaryType): void
    {
        $this->salaryType = $salaryType;
    }

    public function getFacebookApplyData(): ?array
    {
        return $this->facebookApplyData;
    }

    /**
     * Valid Nodes:
     *
     *   (string) application-callback-url
     *   (string) custom-questions-url
     *   (array) form-config
     *      (string) email-field
     *      (string) phone-number-field
     *      (string) work-experience-field
     */
    public function setFacebookApplyData(array $facebookApplyData): void
    {
        $this->facebookApplyData = $facebookApplyData;
    }

    public function toArray(): array
    {
        $result = [];

        $class = new \ReflectionClass(__CLASS__);

        foreach ($class->getMethods() as $method) {
            if (!$method->isPublic()) {
                continue;
            }

            if (str_starts_with($method->getName(), 'get')) {
                $value = $method->invoke($this);

                if (empty($value)) {
                    continue;
                }

                $propName = preg_replace_callback('/([A-Z])/', static function ($c) {
                    return '-' . strtolower($c[1]);
                }, lcfirst(substr($method->getName(), 3)));

                $result[$propName] = $method->invoke($this);
            }
        }

        return $result;
    }
}
