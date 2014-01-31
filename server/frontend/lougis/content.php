<?php
namespace Lougis\frontend\lougis;

require_once(PATH_SERVER.'abstracts/Frontend.php');
class Content extends \Lougis\abstracts\Frontend {

	public function jsonContent() {
        $title = null;
        $items = array();
        switch($_REQUEST['url']) {
            case "/tietokannat":
                $title = "Tietokannat";
                $items[] = array(
                    "xtype" => "container",
                    "html" => "tietokannat toimaa"
                );
            break;
            case "/etusivu":
                $title = "Etusivu";
                $items[] = array(
                    "xtype" => "container",
                    "html" => '<div id="intro"><b>Tervetuloa Aluetietopalvelun hallintaan.</b><br/>Valitse "Työkalut”-valikosta hallittava osio.</div>'
                );
            break;
            case "/tools/users_and_groups":
                $title = "Käyttäjien ja ryhmien hallinta";
                $items[] = array(
                    "xtype" => "usersandgroups"
                );
            break;
            case "/tools/cms":
                $title = "Sisällönhallinta";
                $items[] = array(
                    "xtype" => "cms"
                );
            break;
            case "/tools/news":
                $title = "Ajankohtaista";
                $items[] = array(
                    "xtype" => "news"
                );
            break;
            case "/ymparisto/toimenpiteet":
                $title = "Toimenpiteiden arvioinnit";
                $items[] = array(
                    "xtype" => "toimenpide"
                );
            break;
            case "/tools/charts":
                $title = "Tilastot";
                $items[] = array(
                    "xtype" => "charts"
                );
            break;
            default:
                $title = "Virhe";
                $items[] = array(
                    "xtype" => "container",
                    "html" => "Sivua ei löydy."
                );
        }
        $res = array(
            "success" => !empty($title),
            "title" => $title,
            "items" => $items

        );
        $this->jsonOut($res);

	}
}
?>