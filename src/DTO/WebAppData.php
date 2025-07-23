<?php

namespace ItHealer\Telegram\DTO;

use ItHealer\Telegram\Abstract\DTO;

class WebAppData extends DTO
{
   protected function required(): array
   {
       return ['data', 'button_text'];
   }

   public function data(): string
   {
       return $this->getOrFail('data');
   }

   public function buttonText(): string
   {
       return $this->getOrFail('button_text');
   }
}
