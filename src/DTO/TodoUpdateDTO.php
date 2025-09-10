<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TodoUpdateDTO
{
  public function __construct(
    public ?string $title,

    public ?string $description,

    public ?bool $finished
  ) {}
}
