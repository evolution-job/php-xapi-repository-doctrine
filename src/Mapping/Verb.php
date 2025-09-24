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

use Xabbuh\XApi\Model\IRI;
use Xabbuh\XApi\Model\LanguageMap;
use Xabbuh\XApi\Model\Verb as VerbModel;

/**
 * A {@link Verb} mapped to a storage backend.
 *
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Verb
{
    public string $id;

    public int $identifier;

    public ?array $display = null;

    public function getModel(): VerbModel
    {
        $display = null;

        if (null !== $this->display) {
            $display = LanguageMap::create($this->display);
        }

        return new VerbModel(IRI::fromString($this->id), $display);
    }

    public static function fromModel(VerbModel $verbModel): self
    {
        $verb = new self();
        $verb->id = $verbModel->getId()->getValue();

        if (($display = $verbModel->getDisplay()) instanceof LanguageMap) {
            $verb->display = [];

            foreach ($display->languageTags() as $languageTag) {
                $verb->display[$languageTag] = $display[$languageTag];
            }
        }

        return $verb;
    }
}
