<?php

defined('__MEICAN') or die("Invalid access.");

include_once 'libs/controller.php';

include_once 'apps/circuits/models/flow_info.php';
include_once 'apps/circuits/controllers/reservations.php';

include_once 'apps/topology/models/domain_info.php';
include_once 'apps/topology/models/topology.php';
include_once 'apps/topology/models/meican_info.php';

require_once 'includes/nuSOAP/lib/nusoap.php';
include_once 'apps/circuits/models/oscars_reservation.php';

class flows extends Controller {


    public function add() {
        
        $src_urn = Common::POST("src_urn");
        $dst_urn = Common::POST("dst_urn");

        if ($src_urn && $dst_urn) {

            $new_flow = new flow_info();

            $meican = new meican_info();

            $new_flow->src_meican_id = $meican->getLocalMeicanId();
            $new_flow->src_urn_string = $src_urn;

            $new_flow->dst_meican_id = $meican->getLocalMeicanId();
            $new_flow->dst_urn_string = $dst_urn;

            $new_flow->path = Common::POST("path");

            if (Common::POST("src_vlanType") == "untagged") {
                // src VLAN untagged
                $new_flow->src_vlan = 0;
            } else {
                // src VLAN tagged
                $new_flow->src_vlan = Common::POST("src_vlan");
            }

            if (Common::POST("dst_vlanType") == "untagged") {
                // dst VLAN untagged
                $new_flow->dst_vlan = 0;
            } else {
                // dst VLAN tagged
                $new_flow->dst_vlan = Common::POST("dst_vlan");
            }

            return $new_flow->insert();
        } else
            return FALSE;
    }
}
?>
