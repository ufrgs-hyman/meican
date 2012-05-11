<?php

include_once 'libs/Model/model.php';

class client_info extends Model {
    var $displayField = "alias";

    public function client_info() {
        $this->setTableName("client_info");

        // Add all table attributes
        $this->addAttribute("cli_id", "INTEGER", true, false, false);

        $this->addAttribute("alias", "VARCHAR");
        
        $this->addAttribute("ip_dcn", "VARCHAR");
        $this->addAttribute("ip_internet", "VARCHAR");
        $this->addAttribute("mac_address", "VARCHAR");
        
        $this->addAttribute("urn_id", "INTEGER");
    }
    
    /**
     *
     * @param string $reference A string representing an IP address, an alias or a MAC address
     * @return EndpointObject Returns the endpoint object if the query was sucessful, false othewise
     */
    static public function getBestEndpoint($reference) {
        Log::write("debug", "calculando edp");
        if (!$reference) {
            return false;
        }

        $sql = "SELECT * FROM `client_info`";
        $sql .= " WHERE `alias`='$reference' OR `ip_dcn`='$reference' OR `ip_internet`='$reference' OR `mac_address`='$reference'";
        $result = parent::querySql($sql, 'client_info');

        if ($result) {
            //Log::write("debug", "achou no banco");
            if (count($result) == 1) {
                // retornou apenas um resultado
                $urn_info = new urn_info();
                $urn_info->urn_id = $result[0]->urn_id;
                if ($urn_res = $urn_info->fetch(false)) {
                    $urn = $urn_res[0];
                } else
                    return false;

                $dom_id = null;
                $aco = new Acos($urn->net_id, 'network_info');
                if ($aco_parent = $aco->getParentNodes()) {
                    if ($aco_parent[0]->model = 'domain_info') {
                        $dom_id = $aco_parent[0]->obj_id;
                    }
                }

                if ($dom_id) {

                    $endpoint = new stdClass();
                    $endpoint->domain = $dom_id;
                    $endpoint->network = $urn->net_id;
                    $endpoint->device = $urn->dev_id;
                    $endpoint->port = $urn->port;

                    // se precisar pegar mais detalhes do endpoint
                    //$endpoint = MeicanTopology::getURNDetails($dom_id, $urn->urn_string);

                    //Log::write("debug", "sucesso, retornando...");
                    return $endpoint;
                } else {
                    return false;
                }
            } else {
                // tratar ambiguidade
                Log::write("debug", "ambiguo");
                return false;
            }
        } else {
            // try step 2 -> dynamic
            Log::write("debug", "nao achou, deve seguir passo 2");
            return false;
        }
    }
    
}