<?php

/*
 * This file is part of the xAPI package.
 *
 * (c) Christian Flothmann <christian.flothmann@xabbuh.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace XApi\Repository\Doctrine\Mapping;

use SplObjectStorage;
use Xabbuh\XApi\Model\Extensions as ExtensionsModel;
use Xabbuh\XApi\Model\IRI;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Extensions
{
    public int $identifier;

    public $extensions;

    public static function fromModel(ExtensionsModel $extensionsModel): self
    {
        $extensions = new self();
        $extensions->extensions = [];

        foreach ($extensionsModel->getExtensions() as $extension) {
            $extensions->extensions[$extension->getValue()] = $extensionsModel[$extension];
        }

        return $extensions;
    }

    public function getModel(): ExtensionsModel
    {
        $extensions = new SplObjectStorage();

        foreach ($this->extensions as $key => $extension) {
            $extensions->attach(IRI::fromString($key), $extension);
        }

        return new ExtensionsModel($extensions);
    }
}
