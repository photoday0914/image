<?php

namespace Intervention\Image\Drivers\Imagick\Modifiers;

use ImagickDraw;
use Intervention\Image\Drivers\Abstract\Modifiers\AbstractDrawModifier;
use Intervention\Image\Drivers\Imagick\Color;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ModifierInterface;
use Intervention\Image\Interfaces\ColorInterface;

class DrawRectangleModifier extends AbstractDrawModifier implements ModifierInterface
{
    public function apply(ImageInterface $image): ImageInterface
    {
        // setup rectangle
        $drawing = new ImagickDraw();
        $drawing->setFillColor($this->getBackgroundColor()->getPixel());
        if ($this->drawable()->hasBorder()) {
            $drawing->setStrokeColor($this->getBorderColor()->getPixel());
            $drawing->setStrokeWidth($this->drawable()->getBorderSize());
        }

        // build rectangle
        $drawing->rectangle(
            $this->position->getX(),
            $this->position->getY(),
            $this->position->getX() + $this->drawable()->bottomRightPoint()->getX(),
            $this->position->getY() + $this->drawable()->bottomRightPoint()->getY()
        );

        $image->eachFrame(function ($frame) use ($drawing) {
            $frame->getCore()->drawImage($drawing);
        });

        return $image;
    }

    protected function getBackgroundColor(): ColorInterface
    {
        $color = parent::getBackgroundColor();
        if (!is_a($color, Color::class)) {
            throw new DecoderException('Unable to decode background color.');
        }

        return $color;
    }

    protected function getBorderColor(): ColorInterface
    {
        $color = parent::getBorderColor();
        if (!is_a($color, Color::class)) {
            throw new DecoderException('Unable to decode border color.');
        }

        return $color;
    }
}