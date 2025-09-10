<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TodoDTO
{
  public function __construct(
    #[Assert\NotBlank]
    public string $title,

    public string $description,

    public bool $finished = false
  ) {}
}
