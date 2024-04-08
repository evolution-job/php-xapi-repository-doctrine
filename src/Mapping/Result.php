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

use Xabbuh\XApi\Model\Result as ResultModel;
use Xabbuh\XApi\Model\Score;

/**
 * @author Christian Flothmann <christian.flothmann@xabbuh.de>
 */
class Result
{
    public int $identifier;

    public bool $hasScore;

    public ?float $scaled = null;

    public ?float $raw = null;

    public ?float $min = null;

    public ?float $max = null;

    public ?bool $success = null;

    public ?bool $completion = null;

    public ?string $response = null;

    public ?string $duration = null;

    public ?Extensions $extensions = null;

    public static function fromModel(ResultModel $resultModel): self
    {
        $result = new self();
        $result->success = $resultModel->getSuccess();
        $result->completion = $resultModel->getCompletion();
        $result->response = $resultModel->getResponse();
        $result->duration = $resultModel->getDuration();

        if (($score = $resultModel->getScore()) instanceof Score) {
            $result->hasScore = true;
            $result->scaled = $score->getScaled();
            $result->raw = $score->getRaw();
            $result->min = $score->getMin();
            $result->max = $score->getMax();
        } else {
            $result->hasScore = false;
        }

        if (($extensions = $resultModel->getExtensions()) instanceof \Xabbuh\XApi\Model\Extensions) {
            $result->extensions = Extensions::fromModel($extensions);
        }

        return $result;
    }

    public function getModel(): ResultModel
    {
        $extensions = $this->extensions?->getModel();

        $score = null;

        if ($this->hasScore) {
            $score = new Score($this->scaled, $this->raw, $this->min, $this->max);
        }

        return new ResultModel($score, $this->success, $this->completion, $this->response, $this->duration, $extensions);
    }
}
