<?php

namespace keepinv;

use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerDeathEvent;
use onebone\economyapi\EconomyAPI;
use pocketmine\event\Listener;
use pocketmine\Server;
use pocketmine\Player;

class main extends PluginBase implements Listener{

public function onEnable(){
  $this->getLogger()->info("on");
  $this->getServer()->getPluginManager()->registerEvents($this, $this);
  if(!class_exists(\onebone\economyapi\EconomyAPI::class)) {
        $this->getLogger()->warning("EconomyAPI is not installed, plugin disabling.");
        $this->getServer()->getPluginManager()->disablePlugin($this);
     }
}
public function onDisable(){
  $this->getLogger()-info("off");
}
/*
*Oyuncunun belirlenen fiyata KEEPINVENTORY almasını sağlıyoruz
*/
public function onCommand(CommandSender $player, Command $comm, string $label, array $args) :bool{
  if($comm->getName() === "keepinv"){
    $fiyat = 5000;
    $para = EconomyAPI::getInstance()->myMoney($player);
    if($para >= 5000){
    $this->getConfig()->setNested($player->getName(), ($this->getConfig()->getNested($player->getName()) ?? 0) + 1);
    EconomyAPI::getInstance()->reduceMoney($player, $fiyat);
    $this->getConfig()->save();
    $this->getConfig()->reload();
    $player->sendMessage("KEEPINVENTORY satın aldın!");
    $myparsomen = $this->getConfig()->getNested($player->getName());
    $player->sendMessage("$myparsomen adet KEEPINVENTORY hakkın var.");
    } else{
      $player->sendMessage("Yetersiz para!");
    }
  }
  return true;
}
/*
*Oyuncunun ölme eventi
*Eğer KEEPINVENTORY almış ise eşyaları gitmeyecek
*/
public function keep(PlayerDeathEvent $event){
  $player = $event->getPlayer();
  $myparsomen = $this->getConfig()->getNested($player->getName() ?? 0);
  if($myparsomen > 0){
    $event->setKeepInventory(true);
    $this->getConfig()->setNested($player->getName(), ($this->getConfig()->getNested($player->getName()) ?? 0) - 1);
    $this->getConfig()->save();
    $this->getConfig()->reload();
    $event->getPlayer()->sendMessage("$myparsomen adet KEEPINVENORY hakkın kaldı.");
    if($myparsomen = 0){
      $event->getPlayer()->sendMessage("KEEPINVENTORY hakkın bitti. Lütfen yenisini satın al.");
    }
  } else{
    $event->setKeepInventory(false);
  }
}

}