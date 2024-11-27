<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Event\Model;

use Pimcore\Event\Traits\ArgumentsAwareTrait;
use Pimcore\Mail;
use Symfony\Contracts\EventDispatcher\Event;

class MailEvent extends Event
{
    use ArgumentsAwareTrait;

    protected Mail $mail;

    public function __construct(Mail $mail, array $arguments = [])
    {
        $this->mail = $mail;
        $this->arguments = $arguments;
    }

    public function getMail(): Mail
    {
        return $this->mail;
    }

    public function setMail(Mail $mail): static
    {
        $this->mail = $mail;

        return $this;
    }
}
