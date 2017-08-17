<?php
/**
 * @copyright Copyright (c) 2012-2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

namespace meican\circuits\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;

use meican\nsi\NSIParser;
use meican\aaa\RbacController;
use meican\base\utils\DateUtils;
use meican\circuits\models\Reservation;
use meican\circuits\models\Connection;
use meican\circuits\models\ConnectionAuth;
use meican\circuits\models\ConnectionPath;
use meican\circuits\models\CircuitsPreference;
use meican\circuits\models\Protocol;
use meican\circuits\models\ConnectionEvent;
use meican\circuits\models\ReservationPath;
use meican\circuits\models\CircuitNotification;
use meican\circuits\forms\ReservationForm;
use meican\circuits\forms\ReservationSearch;
use meican\topology\models\Port;
use meican\topology\models\Domain;
use meican\topology\models\Device;
use meican\topology\models\Network;
use meican\topology\models\Service;

/**
 * @author Maurício Quatrin Guerreiros
 */
class ReservationController extends RbacController {

    public $enableCsrfValidation = false;
    
    public function actionCreate() {
        return $this->render('create/create2',[
            'domains'=>Domain::find()->asArray()->all(),
            'reserveForm' => new ReservationForm]);
    }

    public function actionData() {
        return '{
    "domains": {
        "jgn-x.jp": {
            "nsa": {
                "jgn-x.jp:2013:nsa": {
                    "services": {
                        "http://ns.ps.jgn-x.jp/NSI/jgn-x_jp_2013_nml.xml": "NSI_TD_2_0",
                        "https://glambda.dcn.jgn-x.jp:8443/connectionprovider": "NSI_CSP_2_0"
                    },
                    "name": "jgn-x.jp",
                    "type": "UPA",
                    "lat": "35.69",
                    "lng": "137.765",
                    "peerings": [
                        "aist.go.jp:2013:nsa:nsi-aggr"
                    ]
                }
            }
        },
        "openflow.netherlight.net": {
            "nets": {
                "openflow.netherlight.net:2016:topology": {
                    "name": "openflow.netherlight.net:2016",
                    "biports": {
                        "openflow.netherlight.net:2016:topology:netherlight-1": {
                            "port": "netherlight-1",
                            "uniports": {
                                "openflow.netherlight.net:2016:topology:netherlight-1-in": {
                                    "type": "IN",
                                    "vlan": "200-999,1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:netherlight-of-1-out"
                                },
                                "openflow.netherlight.net:2016:topology:netherlight-1-out": {
                                    "type": "OUT",
                                    "vlan": "200-999,1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:netherlight-of-1-in"
                                }
                            }
                        },
                        "openflow.netherlight.net:2016:topology:ps2": {
                            "port": "ps2",
                            "uniports": {
                                "openflow.netherlight.net:2016:topology:ps2-in": {
                                    "type": "IN",
                                    "vlan": "200-999,1779-1799"
                                },
                                "openflow.netherlight.net:2016:topology:ps2-out": {
                                    "type": "OUT",
                                    "vlan": "200-999,1779-1799"
                                }
                            }
                        },
                        "openflow.netherlight.net:2016:topology:ps1": {
                            "port": "ps1",
                            "uniports": {
                                "openflow.netherlight.net:2016:topology:ps1-in": {
                                    "type": "IN",
                                    "vlan": "200-999,1779-1799"
                                },
                                "openflow.netherlight.net:2016:topology:ps1-out": {
                                    "type": "OUT",
                                    "vlan": "200-999,1779-1799"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "openflow.netherlight.net:2016:nsa": {
                    "services": {
                        "https://192.87.102.20:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://192.87.102.20:9443/NSI/openflow.netherlight.net:2016.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "openflow.netherlight.net",
                    "type": "UPA",
                    "lat": null,
                    "lng": null
                }
            }
        },
        "aist.go.jp": {
            "nets": {
                "aist.go.jp:2013:topology": {
                    "name": "aist.go.jp",
                    "biports": {
                        "aist.go.jp:2013:topology:bi-ps": {
                            "port": null,
                            "uniports": {
                                "aist.go.jp:2013:topology:ps-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "aist.go.jp:2013:topology:ps-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "aist.go.jp:2013:topology:bi-video": {
                            "port": null,
                            "uniports": {
                                "aist.go.jp:2013:topology:video-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "aist.go.jp:2013:topology:video-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "aist.go.jp:2013:topology:bi-se1": {
                            "port": null,
                            "uniports": {
                                "aist.go.jp:2013:topology:se1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "aist.go.jp:2013:topology:se1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "aist.go.jp:2013:topology:bi-se2": {
                            "port": null,
                            "uniports": {
                                "aist.go.jp:2013:topology:se2-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "aist.go.jp:2013:topology:se2-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "aist.go.jp:2013:topology:bi-aist-sinet": {
                            "port": null,
                            "uniports": {
                                "aist.go.jp:2013:topology:sinet-aist": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:sinet_aist"
                                },
                                "aist.go.jp:2013:topology:aist-sinet": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:aist_sinet"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "aist.go.jp:2013:nsa:nsi-aggr": {
                    "services": {
                        "https://163.220.30.174:28443/nsi2/services/ConnectionProvider": "NSI_CSP_2_0"
                    },
                    "name": "aist-nsi-aggr",
                    "type": "AGG",
                    "lat": "36.06",
                    "lng": "140.133",
                    "peerings": [
                        "aist.go.jp:2013:nsa",
                        "sinet.ac.jp:2013:nsa",
                        "icair.org:2013:nsa:nsi-am-sl",
                        "netherlight.net:2013:nsa:safnari"
                    ]
                },
                "aist.go.jp:2013:nsa": {
                    "services": {
                        "https://163.220.30.174:28443/NSI/aist.go.jp:2013:nml.xml": "NSI_TD_2_0",
                        "https://163.220.30.173:22311/aist_upa/services/ConnectionProvider": "NSI_CSP_2_0"
                    },
                    "name": "aist.go.jp",
                    "type": "UPA",
                    "lat": "36.06",
                    "lng": "140.133",
                    "peerings": [
                        "aist.go.jp:2013:nsa:nsi-aggr"
                    ]
                }
            }
        },
        "sinet.ac.jp": {
            "nets": {
                "sinet.ac.jp:2013:topology": {
                    "name": "sinet.ac.jp",
                    "biports": {
                        "sinet.ac.jp:2013:topology:bi-sinet_nii-chiba": {
                            "port": "sinet_nii-chiba",
                            "uniports": {
                                "sinet.ac.jp:2013:topology:nii-chiba_sinet": {
                                    "type": "IN",
                                    "vlan": "2030-2049"
                                },
                                "sinet.ac.jp:2013:topology:sinet_nii-chiba": {
                                    "type": "OUT",
                                    "vlan": "2030-2049"
                                }
                            }
                        },
                        "sinet.ac.jp:2013:topology:bi-sinet_manlan": {
                            "port": "sinet-to-pacwave-los_angeles",
                            "uniports": {
                                "sinet.ac.jp:2013:topology:manlan_sinet": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_6:+:out"
                                },
                                "sinet.ac.jp:2013:topology:sinet_manlan": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_6:+:in"
                                }
                            }
                        },
                        "sinet.ac.jp:2013:topology:bi-sinet_pacificwave": {
                            "port": "sinet-to-manlan",
                            "uniports": {
                                "sinet.ac.jp:2013:topology:pacificwave_sinet": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:sinet-los_angeles-out"
                                },
                                "sinet.ac.jp:2013:topology:sinet_pacificwave": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:sinet-los_angeles-in"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "sinet.ac.jp:2013:nsa:nsi-aggr": {
                    "services": {
                        "https://157.1.137.181:28443/nsi2/services/ConnectionProvider": "NSI_CSP_2_0"
                    },
                    "name": "sinet-nsi-aggr",
                    "type": "AGG",
                    "lat": "35.693",
                    "lng": "139.758",
                    "peerings": [
                        "sinet.ac.jp:2013:nsa",
                        "aist.go.jp:2013:nsa",
                        "aist.go.jp:2013:nsa:nsi-aggr",
                        "jgn-x.jp:2013:nsa",
                        "geant.net:2013:nsa",
                        "es.net:2013:nsa:nsi-aggr-west",
                        "icair.org:2013:nsa:nsi-am-sl"
                    ]
                },
                "sinet.ac.jp:2013:nsa": {
                    "services": {
                        "https://150.100.12.170:28443/sinet_upa/services/connectionprovider": "NSI_CSP_2_0",
                        "https://raw.githubusercontent.com/AutomatedGOLE/nsi-discovery-documents/master/topology/sinet.ac.jp_2013_nml.xml": "NSI_TD_2_0"
                    },
                    "name": "sinet.ac.jp",
                    "type": "UPA",
                    "lat": "35.693",
                    "lng": "139.758",
                    "peerings": [
                        "sinet.ac.jp:2013:nsa:nsi-aggr"
                    ]
                }
            }
        },
        "surfnet.nl": {
            "nsa": {
                "surfnet.nl:1990:nsa:bod7": {
                    "services": {
                        "https://bod.surfnet.nl/nsi-topology/netherlight7": "NSI_TD_2_0",
                        "https://bod.surfnet.nl/nsi-topology/production7": "NSI_TD_2_0",
                        "https://bod.surfnet.nl/nsi/v2/provider": "NSI_CSP_2_0"
                    },
                    "name": "SURFnet7 production",
                    "type": "UPA",
                    "lat": "52.3567",
                    "lng": "4.954585"
                },
                "surfnet.nl:1990:nsa:bod-acc": {
                    "services": {
                        "https://bod.acc.dlp.surfnet.nl/nsi-topology/netherlight-testbed7": "NSI_TD_2_0",
                        "https://bod.acc.dlp.surfnet.nl/nsi-topology/testbed7": "NSI_TD_2_0",
                        "https://bod.acc.dlp.surfnet.nl/nsi/v2/provider": "NSI_CSP_2_0"
                    },
                    "name": "SURFnet7 testbed",
                    "type": "UPA",
                    "lat": "52.3567",
                    "lng": "4.954585"
                }
            },
            "nets": {
                "surfnet.nl:1990:netherlight7": {
                    "name": "netherlight7",
                    "biports": {
                        "netherlight7:2c:39:c1:38:e0:00-4-1": {
                            "port": "Asd001A_8700_07 4/1 UvA (SNE)",
                            "lat":52.3567,
                            "lng":4.954585,
                            "uniports": {
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-4-1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                },
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-4-1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "netherlight7:2c:39:c1:38:e0:00-7-2": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_8700_07 7/2 NORDUnet (nl-sar2-nordunet xe-0/0/3)",
                            "uniports": {
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-7-2-out": {
                                    "type": "OUT",
                                    "vlan": "2-4095"
                                },
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-7-2-in": {
                                    "type": "IN",
                                    "vlan": "2-4095"
                                }
                            }
                        },
                        "netherlight7:2c:39:c1:38:e0:00-10-1": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_8700_07 10/1 MANLAN (via Hibernia)",
                            "uniports": {
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-10-1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1819,3400-3598,4006-4019"
                                },
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-10-1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1819,3400-3598,4006-4019"
                                }
                            }
                        },
                        "netherlight7:2c:39:c1:38:e0:00-8-1": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_8700_07 8/1 StarLight/iCAIR (via GEANT Open London)",
                            "uniports": {
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-8-1-out": {
                                    "type": "OUT",
                                    "vlan": "4020-4039"
                                },
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-8-1-in": {
                                    "type": "IN",
                                    "vlan": "4020-4039"
                                }
                            }
                        },
                        "netherlight7:2c:39:c1:38:e0:00-5-13": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_8700_07 5/13 iperf1 eth2",
                            "uniports": {
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-5-13-out": {
                                    "type": "OUT",
                                    "vlan": "3000-4000"
                                },
                                "surfnet.nl:1990:netherlight7:2c:39:c1:38:e0:00-5-13-in": {
                                    "type": "IN",
                                    "vlan": "3000-4000"
                                }
                            }
                        }
                    }
                },
                "surfnet.nl:1990:testbed7": {
                    "name": "testbed7",
                    "biports": {
                        "testbed7:3821": {
                            "port": "Asd001A_5410-01T 1/32",
                            "lat":52.3567,
                            "lng":4.954585,
                            "uniports": {
                                "surfnet.nl:1990:testbed7:3821-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2019"
                                },
                                "surfnet.nl:1990:testbed7:3821-in": {
                                    "type": "IN",
                                    "vlan": "2000-2019"
                                }
                            }
                        },
                        "testbed7:14895": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_5410-02T 6/3 Anritsu tester poort 2/4",
                            "uniports": {
                                "surfnet.nl:1990:testbed7:14895-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2019"
                                },
                                "surfnet.nl:1990:testbed7:14895-in": {
                                    "type": "IN",
                                    "vlan": "2000-2019"
                                }
                            }
                        },
                        "testbed7:netherlight-1": {
                            "lat":52.3567,
                            "lng":4.954585,
                            "port": "Asd001A_5150-06T 2.2 Asd001A_Anritsu_01_2/2",
                            "uniports": {
                                "surfnet.nl:1990:testbed7:netherlight-1-out": {
                                    "type": "OUT",
                                    "vlan": "200-299",
                                    "aliasUrn": "netherlight.net:2013:topology:surfnet-1-in"
                                },
                                "surfnet.nl:1990:testbed7:netherlight-1-in": {
                                    "type": "IN",
                                    "vlan": "200-299",
                                    "aliasUrn": "netherlight.net:2013:topology:surfnet-1-out"
                                }
                            }
                        }
                    }
                }
            }
        },
        "funet.fi": {
            "nets": {
                "funet.fi:2013:topology": {
                    "name": "funet.fi",
                    "biports": {
                        "funet.fi:2013:topology:espoo1-nordunet": {
                            "port": "espoo1-nordunet",
                            "uniports": {
                                "funet.fi:2013:topology:espoo1-nordunet-in": {
                                    "type": "IN",
                                    "vlan": "2031-2035",
                                    "aliasUrn": "nordu.net:2013:topology:funet-out"
                                },
                                "funet.fi:2013:topology:espoo1-nordunet-out": {
                                    "type": "OUT",
                                    "vlan": "2031-2035",
                                    "aliasUrn": "nordu.net:2013:topology:funet-in"
                                }
                            }
                        },
                        "funet.fi:2013:topology:csc1-csc-mankeli": {
                            "port": "csc1-csc-mankeli",
                            "uniports": {
                                "funet.fi:2013:topology:csc1-csc-mankeli-in": {
                                    "type": "IN",
                                    "vlan": "1001-1003,1780-1799"
                                },
                                "funet.fi:2013:topology:csc1-csc-mankeli-out": {
                                    "type": "OUT",
                                    "vlan": "1001-1003,1780-1799"
                                }
                            }
                        },
                        "funet.fi:2013:topology:csc1-csc-bmi-eyrg": {
                            "port": "csc1-csc-bmi-eyrg",
                            "uniports": {
                                "funet.fi:2013:topology:csc1-csc-bmi-eyrg-in": {
                                    "type": "IN",
                                    "vlan": "77-78"
                                },
                                "funet.fi:2013:topology:csc1-csc-bmi-eyrg-out": {
                                    "type": "OUT",
                                    "vlan": "77-78"
                                }
                            }
                        },
                        "funet.fi:2013:topology:test1-autobahn-2": {
                            "port": "test1-autobahn-2",
                            "uniports": {
                                "funet.fi:2013:topology:test1-autobahn-2-in": {
                                    "type": "IN",
                                    "vlan": "1000-4094"
                                },
                                "funet.fi:2013:topology:test1-autobahn-2-out": {
                                    "type": "OUT",
                                    "vlan": "1000-4094"
                                }
                            }
                        },
                        "funet.fi:2013:topology:test1-autobahn-4": {
                            "port": "test1-autobahn-4",
                            "uniports": {
                                "funet.fi:2013:topology:test1-autobahn-4-in": {
                                    "type": "IN",
                                    "vlan": "1000-4094"
                                },
                                "funet.fi:2013:topology:test1-autobahn-4-out": {
                                    "type": "OUT",
                                    "vlan": "1000-4094"
                                }
                            }
                        },
                        "funet.fi:2013:topology:espoo1-geant": {
                            "port": "espoo1-geant",
                            "uniports": {
                                "funet.fi:2013:topology:espoo1-geant-in": {
                                    "type": "IN",
                                    "vlan": "2026-2030",
                                    "aliasUrn": "geant.net:2013:topology:funet-out"
                                },
                                "funet.fi:2013:topology:espoo1-geant-out": {
                                    "type": "OUT",
                                    "vlan": "2026-2030",
                                    "aliasUrn": "geant.net:2013:topology:funet-in"
                                }
                            }
                        }
                    }
                }
            }
        },
        "snvaca.pacificwave.net": {
            "nets": {
                "snvaca.pacificwave.net:2016:topology": {
                    "name": "snvaca.pacificwave.net:2016",
                    "biports": {
                        "snvaca.pacificwave.net:2016:topology:esnet-sunnyvale": {
                            "port": "esnet-sunnyvale",
                            "uniports": {
                                "snvaca.pacificwave.net:2016:topology:esnet-sunnyvale-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::sunn-cr5:8_1_1:pacwave:out"
                                },
                                "snvaca.pacificwave.net:2016:topology:esnet-sunnyvale-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::sunn-cr5:8_1_1:pacwave:in"
                                }
                            }
                        },
                        "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e7_2": {
                            "port": "snvl2-pw-sw-1_e7_2",
                            "uniports": {
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e7_2-in": {
                                    "type": "IN",
                                    "vlan": "981,1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:losa2-pw-sw-1_e1_1-out"
                                },
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e7_2-out": {
                                    "type": "OUT",
                                    "vlan": "981,1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:losa2-pw-sw-1_e1_1-in"
                                }
                            }
                        },
                        "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e1_1": {
                            "port": "snvl2-pw-sw-1_e1_1",
                            "uniports": {
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e1_1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e1_1-out"
                                },
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e1_1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e1_1-in"
                                }
                            }
                        },
                        "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e3_2": {
                            "port": "snvl2-pw-sw-1_e3_2",
                            "uniports": {
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e3_2-in": {
                                    "type": "IN",
                                    "vlan": "981",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e3_2-out"
                                },
                                "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e3_2-out": {
                                    "type": "OUT",
                                    "vlan": "981",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e3_2-in"
                                }
                            }
                        },
                        "snvaca.pacificwave.net:2016:topology:irnc-10g01.snvaca": {
                            "port": "irnc-10g01.snvaca",
                            "uniports": {
                                "snvaca.pacificwave.net:2016:topology:irnc-10g01.snvaca-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "snvaca.pacificwave.net:2016:topology:irnc-10g01.snvaca-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "snvaca.pacificwave.net:2016:nsa": {
                    "services": {
                        "https://nsi0.snvaca.pacificwave.net:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://nsi0.snvaca.pacificwave.net:9443/NSI/snvaca.pacificwave.net:2016.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "snvaca.pacificwave.net",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            }
        },
        "southernlight.net.br": {
            "nets": {
                "southernlight.net.br:2013:topology": {
                    "name": "southernlight.net.br:2013",
                    "biports": {
                        "southernlight.net.br:2013:topology:ampath": {
                            "port": "ampath",
                            "uniports": {
                                "southernlight.net.br:2013:topology:ampath-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "ampath.net:2013:topology:southernlight-out"
                                },
                                "southernlight.net.br:2013:topology:ampath-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "ampath.net:2013:topology:southernlight-in"
                                }
                            }
                        },
                        "southernlight.net.br:2013:topology:bi-geant": {
                            "port": "bi-geant",
                            "uniports": {
                                "southernlight.net.br:2013:topology:bi-geant-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "geant.net:2013:topology:bi-southernlight-out"
                                },
                                "southernlight.net.br:2013:topology:bi-geant-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "geant.net:2013:topology:bi-southernlight-in"
                                }
                            }
                        },
                        "southernlight.net.br:2013:topology:bi-rnp": {
                            "port": "bi-rnp",
                            "uniports": {
                                "southernlight.net.br:2013:topology:bi-rnp-in": {
                                    "type": "IN",
                                    "vlan": "1700-1799",
                                    "aliasUrn": "cipo.rnp.br:2013::MXSP:ae4:southernlight:out"
                                },
                                "southernlight.net.br:2013:topology:bi-rnp-out": {
                                    "type": "OUT",
                                    "vlan": "1700-1799",
                                    "aliasUrn": "cipo.rnp.br:2013::MXSP:ae4:southernlight:in"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "southernlight.net.br:2013:nsa": {
                    "services": {
                        "https://southernlight.net.br:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://southernlight.net.br:9443/NSI/southernlight.net.br:2013.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "southernlight.net.br",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            }
        },
        "sttlwa.pacificwave.net": {
            "nets": {
                "sttlwa.pacificwave.net:2016:topology": {
                    "name": "sttlwa.pacificwave.net:2016",
                    "biports": {
                        "sttlwa.pacificwave.net:2016:topology:esnet-seattle": {
                            "port": "esnet-seattle",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:esnet-seattle-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::pnwg-cr5:2_1_1:+:out"
                                },
                                "sttlwa.pacificwave.net:2016:topology:esnet-seattle-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::pnwg-cr5:2_1_1:+:in"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:icair-grp": {
                            "port": "icair-grp",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:icair-grp-in": {
                                    "type": "IN",
                                    "vlan": "1379-1399",
                                    "aliasUrn": "icair.org:2013:topology:pwave-grp-out"
                                },
                                "sttlwa.pacificwave.net:2016:topology:icair-grp-out": {
                                    "type": "OUT",
                                    "vlan": "1379-1399",
                                    "aliasUrn": "icair.org:2013:topology:pwave-grp-in"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:icair-pnwgp_chcg": {
                            "port": "icair-pnwgp_chcg",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:icair-pnwgp_chcg-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "icair.org:2013:topology:pwave-pnwgp_chcg-out"
                                },
                                "sttlwa.pacificwave.net:2016:topology:icair-pnwgp_chcg-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "icair.org:2013:topology:pwave-pnwgp_chcg-in"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e1_1": {
                            "port": "icas-sttlwa01-03_e1_1",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e1_1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e1_1-out"
                                },
                                "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e1_1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e1_1-in"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e3_2": {
                            "port": "icas-sttlwa01-03_e3_2",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e3_2-in": {
                                    "type": "IN",
                                    "vlan": "981",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e3_2-out"
                                },
                                "sttlwa.pacificwave.net:2016:topology:icas-sttlwa01-03_e3_2-out": {
                                    "type": "OUT",
                                    "vlan": "981",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e3_2-in"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:irnc-10g02.sttlwa": {
                            "port": "irnc-10g02.sttlwa",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:irnc-10g02.sttlwa-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "sttlwa.pacificwave.net:2016:topology:irnc-10g02.sttlwa-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "sttlwa.pacificwave.net:2016:topology:irnc-100g01.sttlwa": {
                            "port": "irnc-100g01.sttlwa",
                            "uniports": {
                                "sttlwa.pacificwave.net:2016:topology:irnc-100g01.sttlwa-in": {
                                    "type": "IN",
                                    "vlan": "981,1779-1799"
                                },
                                "sttlwa.pacificwave.net:2016:topology:irnc-100g01.sttlwa-out": {
                                    "type": "OUT",
                                    "vlan": "981,1779-1799"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "sttlwa.pacificwave.net:2016:nsa": {
                    "services": {
                        "https://nsi0.sttlwa.pacificwave.net:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://nsi0.sttlwa.pacificwave.net:9443/NSI/sttlwa.pacificwave.net:2016.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "sttlwa.pacificwave.net",
                    "type": "AGG",
                    "lat": null,
                    "lng": null,
                    "peerings": [
                        "icair.org:2013:nsa",
                        "netherlight.net:2013:nsa:safnari",
                        "snvaca.pacificwave.net:2016:nsa",
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            }
        },
        "cipo.rnp.br": {
            "nets": {
                "cipo.rnp.br:2013:": {
                    "name": "cipo.rnp.br",
                    "biports": {
                        "MXTO": {
                            "port": null,
                            "lat": -10.175300,   
                            "lng":-48.298199,
                            "uniports": {
                                "cipo.rnp.br:2013::MXTO:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXTO:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXBA": {
                            "port": null,
                            "lat": -12.901600 , 
                            "lng": -38.419800,   
                            "uniports": {
                                "cipo.rnp.br:2013::MXBA:ge-3_3_0:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXBA:ge-3_3_0:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXRO:ge-2_3_4:+": {
                            "port": null,
                            "lat":  -8.756550  ,
                            "lng": -63.854900  ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXRO:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXRO:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXSP:ae4:southernlight": {
                            "port": null,
                            "lat": -23.543200  ,
                            "lng": -46.629200  ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXSP:ae4:southernlight:in": {
                                    "type": "IN",
                                    "vlan": "1700-1799",
                                    "aliasUrn": "southernlight.net.br:2013:topology:bi-rnp-out"
                                },
                                "cipo.rnp.br:2013::MXSP:ae4:southernlight:out": {
                                    "type": "OUT",
                                    "vlan": "1700-1799",
                                    "aliasUrn": "southernlight.net.br:2013:topology:bi-rnp-in"
                                }
                            }
                        },
                        "MXSP:ge-2_3_3:+": {
                            "port": null,
                           "lat": -23.543200  ,
                            "lng": -46.629200  ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXSP:ge-2_3_3:+:in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "cipo.rnp.br:2013::MXSP:ge-2_3_3:+:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "MXJPA:ge-2_3_4:+": {
                            "port": null,
                            "lat":  -7.146600  ,
                            "lng":-34.881599 ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXJPA:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXJPA:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXMG:ge-2_3_4:+": {
                            "port": null,
                            "lat":  -19.902700  ,
                            "lng": -43.964001,
                            "uniports": {
                                "cipo.rnp.br:2013::MXMG:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXMG:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXRS:ge-2_3_4:+": {
                            "port": null,
                            "lat":  -30.034599 ,
                            "lng": -51.217701,
                            "uniports": {
                                "cipo.rnp.br:2013::MXRS:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"                                    
                                },
                                "cipo.rnp.br:2013::MXRS:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXRJ:xe-3_0_0:+": {
                            "port": null,
                            "lat": -22.913900  ,
                            "lng": -43.209400 ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXRJ:xe-3_0_0:+:in": {
                                    "type": "IN",
                                    "vlan": "1700-1799"
                                },
                                "cipo.rnp.br:2013::MXRJ:xe-3_0_0:+:out": {
                                    "type": "OUT",
                                    "vlan": "1700-1799"
                                }
                            }
                        },
                        "MXRJ:ae0:+": {
                            "port": null,
                             "lat": -22.913900  ,
                            "lng": -43.209400 ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXRJ:ae0:+:in": {
                                    "type": "IN",
                                    "vlan": "1700-1799"
                                },
                                "cipo.rnp.br:2013::MXRJ:ae0:+:out": {
                                    "type": "OUT",
                                    "vlan": "1700-1799"
                                }
                            }
                        },
                        "MXRJ:ge-2_3_3:+": {
                            "port": null,
                             "lat": -22.913900  ,
                            "lng": -43.209400 ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXRJ:ge-2_3_3:+:in": {
                                    "type": "IN",
                                    "vlan": "1801-1821"
                                },
                                "cipo.rnp.br:2013::MXRJ:ge-2_3_3:+:out": {
                                    "type": "OUT",
                                    "vlan": "1801-1821"
                                }
                            }
                        },
                        "MXSP2:xe-3_0_0:+": {
                            "port": null,
                           "lat": -23.543200  ,
                            "lng": -46.629200  ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXSP2:xe-3_0_0:+:in": {
                                    "type": "IN",
                                    "vlan": "201-299"
                                },
                                "cipo.rnp.br:2013::MXSP2:xe-3_0_0:+:out": {
                                    "type": "OUT",
                                    "vlan": "201-299"
                                }
                            }
                        },
                        "MXSP:ge-2_3_4:+": {
                            "port": null,
                           "lat": -23.543200  ,
                            "lng": -46.629200  ,
                            "uniports": {
                                "cipo.rnp.br:2013::MXSP:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXSP:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        },
                        "MXDF:ge-2_3_4:+": {
                            "port": null,
                            "lat": -15.826700  ,
                            "lng": -47.921799,
                            "uniports": {
                                "cipo.rnp.br:2013::MXDF:ge-2_3_4:+:in": {
                                    "type": "IN",
                                    "vlan": "200-299"
                                },
                                "cipo.rnp.br:2013::MXDF:ge-2_3_4:+:out": {
                                    "type": "OUT",
                                    "vlan": "200-299"
                                }
                            }
                        }

                    }
                }
            },
            "nsa": {
                "cipo.rnp.br:2014:nsa:safnari": {
                    "services": {
                        "https://agg.cipo.rnp.br/dds": "NSI_DS_1_0",
                        "https://agg.cipo.rnp.br/nsi-v2/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "agg.cipo.rnp.br",
                    "type": "AGG",
                    "lat": "-22.862125",
                    "lng": "-43.229733",
                    "peerings": [
                        "cipo.rnp.br:2014:nsa",
                        "ampath.net:2013:nsa",
                        "southernlight.net.br:2013:nsa",
                        "es.net:2013:nsa:nsi-aggr-west",
                        "netherlight.net:2013:nsa:safnari",
                        "geant.net:2013:nsa"
                    ]
                },
                "cipo.rnp.br:2014:nsa": {
                    "services": {
                        "https://idc.cipo.rnp.br:8500/nsi-v2/ConnectionServiceProvider": "NSI_CSP_2_0",
                        "http://idc.cipo.rnp.br/rnp-topology.xml": "NSI_TD_2_0"
                    },
                    "name": "RNP OSCARS uPA",
                    "type": "UPA",
                    "lat": "-22.95513",
                    "lng": "-43.177483",
                    "peerings": [
                        "cipo.rnp.br:2014:nsa:safnari"
                    ]
                }
            }
        },
        "icair.org": {
            "nets": {
                "icair.org:2013:topology": {
                    "name": "icair.org:2013",
                    "biports": {
                        "icair.org:2013:topology:pwave-pnwgp_chcg": {
                            "port": "pwave-pnwgp_chcg",
                            "uniports": {
                                "icair.org:2013:topology:pwave-pnwgp_chcg-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icair-pnwgp_chcg-out"
                                },
                                "icair.org:2013:topology:pwave-pnwgp_chcg-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icair-pnwgp_chcg-in"
                                }
                            }
                        },
                        "icair.org:2013:topology:pwave-grp": {
                            "port": "pwave-grp",
                            "uniports": {
                                "icair.org:2013:topology:pwave-grp-in": {
                                    "type": "IN",
                                    "vlan": "1379-1399",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icair-grp-out"
                                },
                                "icair.org:2013:topology:pwave-grp-out": {
                                    "type": "OUT",
                                    "vlan": "1379-1399",
                                    "aliasUrn": "sttlwa.pacificwave.net:2016:topology:icair-grp-in"
                                }
                            }
                        },
                        "icair.org:2013:topology:ps": {
                            "port": "ps",
                            "uniports": {
                                "icair.org:2013:topology:ps-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "icair.org:2013:topology:ps-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "icair.org:2013:topology:esnet": {
                            "port": "esnet",
                            "uniports": {
                                "icair.org:2013:topology:esnet-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::star-cr5:10_1_8:+:out"
                                },
                                "icair.org:2013:topology:esnet-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "es.net:2013::star-cr5:10_1_8:+:in"
                                }
                            }
                        },
                        "icair.org:2013:topology:manlan": {
                            "port": "manlan",
                            "uniports": {
                                "icair.org:2013:topology:manlan-in": {
                                    "type": "IN",
                                    "vlan": "4006-4019",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_1:al2s:out"
                                },
                                "icair.org:2013:topology:manlan-out": {
                                    "type": "OUT",
                                    "vlan": "4006-4019",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_1:al2s:in"
                                }
                            }
                        },
                        "icair.org:2013:topology:netherlight": {
                            "port": "netherlight",
                            "uniports": {
                                "icair.org:2013:topology:netherlight-in": {
                                    "type": "IN",
                                    "vlan": "4020-4039",
                                    "aliasUrn": "netherlight.net:2013:production7:starlight-1-out"
                                },
                                "icair.org:2013:topology:netherlight-out": {
                                    "type": "OUT",
                                    "vlan": "4020-4039",
                                    "aliasUrn": "netherlight.net:2013:production7:starlight-1-in"
                                }
                            }
                        },
                        "icair.org:2013:topology:icairlab": {
                            "port": "icairlab",
                            "uniports": {
                                "icair.org:2013:topology:icairlab-in": {
                                    "type": "IN",
                                    "vlan": "1779-4039"
                                },
                                "icair.org:2013:topology:icairlab-out": {
                                    "type": "OUT",
                                    "vlan": "1779-4039"
                                }
                            }
                        },
                        "icair.org:2013:topology:krlight": {
                            "port": "krlight",
                            "uniports": {
                                "icair.org:2013:topology:krlight-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "krlight.net:2013:topology:if-krlight-startap"
                                },
                                "icair.org:2013:topology:krlight-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "krlight.net:2013:topology:if-startap-krlight"
                                }
                            }
                        },
                        "icair.org:2013:topology:ampath": {
                            "port": "ampath",
                            "uniports": {
                                "icair.org:2013:topology:ampath-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "ampath.net:2013:topology:starlight-out"
                                },
                                "icair.org:2013:topology:ampath-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "ampath.net:2013:topology:starlight-in"
                                }
                            }
                        },
                        "icair.org:2013:topology:twanet": {
                            "port": "twanet",
                            "uniports": {
                                "icair.org:2013:topology:twanet-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "twaren.nchc.org:2014:topology:ofport3-out"
                                },
                                "icair.org:2013:topology:twanet-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "twaren.nchc.org:2014:topology:ofport3-in"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "icair.org:2013:nsa": {
                    "services": {
                        "https://pmri061.it.northwestern.edu:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://pmri061.it.northwestern.edu:9443/NSI/icair.org:2013.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "icair.org",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                },
                "icair.org:2013:nsa:nsi-am-sl": {
                    "services": {
                        "https://nsi-am-sl.northwestern.edu/dds": "NSI_DS_1_0",
                        "https://nsi-am-sl.northwestern.edu/nsi-v2/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "iCAIR Aggregator",
                    "type": "AGG",
                    "lat": "41.895176",
                    "lng": "-87.6168",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west",
                        "sinet.ac.jp:2013:nsa:nsi-aggr"
                    ]
                }
            }
        },
        "es.net": {
            "nsa": {
                "es.net:2013:nsa:nsi-aggr-west": {
                    "services": {
                        "https://nsi-aggr-west.es.net/discovery": "NSI_DS_1_0",
                        "https://nsi-aggr-west.es.net/nsi-v2/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "nsi-aggr-west",
                    "type": "AGG",
                    "lat": "37.87692",
                    "lng": "-122.25023",
                    "peerings": [
                        "manlan.internet2.edu:2013:nsa",
                        "wix.internet2.edu:2013:nsa",
                        "es.net:2013:nsa",
                        "geant.net:2013:nsa",
                        "netherlight.net:2013:nsa:safnari",
                        "icair.org:2013:nsa",
                        "icair.org:2013:nsa:nsi-am-sl",
                        "caltech.edu:2013:nsa",
                        "cipo.rnp.br:2014:nsa",
                        "cipo.rnp.br:2014:nsa:safnari",
                        "oess.dcn.umnet.umich.edu:2013:nsa",
                        "lsanca.pacificwave.net:2016:nsa",
                        "sinet.ac.jp:2013:nsa:nsi-aggr",
                        "kddilabs.jp:2013:nsa",
                        "snvaca.pacificwave.net:2016:nsa",
                        "sttlwa.pacificwave.net:2016:nsa"
                    ]
                },
                "es.net:2013:nsa": {
                    "services": {
                        "https://oscars.es.net/ConnectionService": "NSI_CSP_2_0"
                    },
                    "name": "ESnet OSCARS uPA",
                    "type": "UPA",
                    "lat": "37.87692",
                    "lng": "-122.25023",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            },
            "nets": {
                "es.net:2013:": {
                    "name": "es.net",
                    "biports": {
                        "chic-cr5:3_2_1:+": {
                            "port": null,
                            "lat":41.8339042,
                            "lng":-88.0123493,
                            "uniports": {
                                "es.net:2013::chic-cr5:3_2_1:+:in": {
                                    "type": "IN",
                                    "vlan": "2-4094"
                                },
                                "es.net:2013::chic-cr5:3_2_1:+:out": {
                                    "type": "OUT",
                                    "vlan": "2-4094"
                                }
                            }
                        },
                        "star-cr5:3_1_1:+": {
                            "port": null,
                            "lat":34.13369,
                            "lng":-118.1236976,
                            "uniports": {
                                "es.net:2013::star-cr5:3_1_1:+:in": {
                                    "type": "IN",
                                    "vlan": "1662-1663"
                                },
                                "es.net:2013::star-cr5:3_1_1:+:out": {
                                    "type": "OUT",
                                    "vlan": "1662-1663"
                                }
                            }
                        },
                        "star-cr5:6_1_1:star-tb1": {
                            "port": null,
                            "lat":34.13369,
                            "lng":-118.1236976,
                            "uniports": {
                                "es.net:2013::star-cr5:6_1_1:star-tb1:in": {
                                    "type": "IN",
                                    "vlan": "2-4094"
                                },
                                "es.net:2013::star-cr5:6_1_1:star-tb1:out": {
                                    "type": "OUT",
                                    "vlan": "2-4094"
                                }
                            }
                        }
                    }
                }
            }
        },
        "kddilabs.jp": {
            "nsa": {
                "kddilabs.jp:2013:nsa": {
                    "services": {
                        "http://210.196.65.114:9352/2013/07/connectionprovider": "NSI_CSP_2_0",
                        "https://raw.githubusercontent.com/AutomatedGOLE/nsi-discovery-documents/master/topology/kddilabs.jp_2013_nml.xml": "NSI_TD_2_0"
                    },
                    "name": "KDDI Labs uPA",
                    "type": "UPA",
                    "lat": "35.879",
                    "lng": "139.517",
                    "peerings": [
                        "jgn-x.jp:2013:nsa",
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            },
            "nets": {
                "kddilabs.jp:2013:topology": {
                    "name": "kddilabs.jp",
                    "biports": {
                        "bi-kddilabs-jgn-x": {
                            "port": null,
                            "lat": 35.879,
                            "lng": 139.517,
                            "uniports": {
                                "kddilabs.jp:2013:topology:kddilabs-jgn-x": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "jgn-x.jp:2013:topology:kddilabs-jgn-x"
                                },
                                "kddilabs.jp:2013:topology:jgn-x-kddilabs": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "jgn-x.jp:2013:topology:jgn-x-kddilabs"
                                }
                            }
                        },
                        "bi-ps": {
                            "port": null,
                            "lat": 35.879,
                            "lng": 139.517,
                            "uniports": {
                                "kddilabs.jp:2013:topology:ps-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "kddilabs.jp:2013:topology:ps-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        }
                    }
                }
            }
        },
        "lsanca.pacificwave.net": {
            "nets": {
                "lsanca.pacificwave.net:2016:topology": {
                    "name": "lsanca.pacificwave.net:2016",
                    "biports": {
                        "lsanca.pacificwave.net:2016:topology:caltech": {
                            "port": "caltech",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:caltech-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "caltech.edu:2013::CER2024:eth2_1:PWave:out"
                                },
                                "lsanca.pacificwave.net:2016:topology:caltech-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "caltech.edu:2013::CER2024:eth2_1:PWave:in"
                                }
                            }
                        },
                        "lsanca.pacificwave.net:2016:topology:sinet-los_angeles": {
                            "port": "sinet-los_angeles",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:sinet-los_angeles-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:sinet_pacificwave"
                                },
                                "lsanca.pacificwave.net:2016:topology:sinet-los_angeles-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:pacificwave_sinet"
                                }
                            }
                        },
                        "lsanca.pacificwave.net:2016:topology:losa2-pw-sw-1_e1_1": {
                            "port": "losa2-pw-sw-1_e1_1",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:losa2-pw-sw-1_e1_1-in": {
                                    "type": "IN",
                                    "vlan": "981,1779-1799",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e7_2-out"
                                },
                                "lsanca.pacificwave.net:2016:topology:losa2-pw-sw-1_e1_1-out": {
                                    "type": "OUT",
                                    "vlan": "981,1779-1799",
                                    "aliasUrn": "snvaca.pacificwave.net:2016:topology:snvl2-pw-sw-1_e7_2-in"
                                }
                            }
                        },
                        "lsanca.pacificwave.net:2016:topology:irnc-100g01.lsanca": {
                            "port": "irnc-100g01.lsanca",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:irnc-100g01.lsanca-in": {
                                    "type": "IN",
                                    "vlan": "981,1779-1799"
                                },
                                "lsanca.pacificwave.net:2016:topology:irnc-100g01.lsanca-out": {
                                    "type": "OUT",
                                    "vlan": "981,1779-1799"
                                }
                            }
                        },
                        "lsanca.pacificwave.net:2016:topology:irnc-10g02.lsanca": {
                            "port": "irnc-10g02.lsanca",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:irnc-10g02.lsanca-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "lsanca.pacificwave.net:2016:topology:irnc-10g02.lsanca-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "lsanca.pacificwave.net:2016:topology:dtn0.lsanca": {
                            "port": "dtn0.lsanca",
                            "uniports": {
                                "lsanca.pacificwave.net:2016:topology:dtn0.lsanca-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "lsanca.pacificwave.net:2016:topology:dtn0.lsanca-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "lsanca.pacificwave.net:2016:nsa": {
                    "services": {
                        "https://nsi0.lsanca.pacificwave.net:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://nsi0.lsanca.pacificwave.net:9443/NSI/lsanca.pacificwave.net:2016.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "lsanca.pacificwave.net",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            }
        },
        "caltech.edu": {
            "nets": {
                "caltech.edu:2013:": {
                    "name": "caltech.edu",
                    "biports": {
                        "caltech.edu:2013::CER2024:eth1_24:+": {
                            "port": null,
                            "uniports": {
                                "caltech.edu:2013::CER2024:eth1_24:+:in": {
                                    "type": "IN",
                                    "vlan": "0,1-4095"
                                },
                                "caltech.edu:2013::CER2024:eth1_24:+:out": {
                                    "type": "OUT",
                                    "vlan": "0,1-4095"
                                }
                            }
                        },
                        "caltech.edu:2013::CER2024:eth2_2:+": {
                            "port": null,
                            "uniports": {
                                "caltech.edu:2013::CER2024:eth2_2:+:in": {
                                    "type": "IN",
                                    "vlan": "0,1-4095"
                                },
                                "caltech.edu:2013::CER2024:eth2_2:+:out": {
                                    "type": "OUT",
                                    "vlan": "0,1-4095"
                                }
                            }
                        },
                        "caltech.edu:2013::CER2024:eth2_3:+": {
                            "port": null,
                            "uniports": {
                                "caltech.edu:2013::CER2024:eth2_3:+:in": {
                                    "type": "IN",
                                    "vlan": "0,1-4095"
                                },
                                "caltech.edu:2013::CER2024:eth2_3:+:out": {
                                    "type": "OUT",
                                    "vlan": "0,1-4095"
                                }
                            }
                        },
                        "caltech.edu:2013::CER2024:eth2_1:esnet": {
                            "port": null,
                            "uniports": {
                                "caltech.edu:2013::CER2024:eth2_1:esnet:in": {
                                    "type": "IN",
                                    "vlan": "3600-3610",
                                    "aliasUrn": "es.net:2013::sunn-cr5:8_1_1:caltech:out"
                                },
                                "caltech.edu:2013::CER2024:eth2_1:esnet:out": {
                                    "type": "OUT",
                                    "vlan": "3600-3610",
                                    "aliasUrn": "es.net:2013::sunn-cr5:8_1_1:caltech:in"
                                }
                            }
                        },
                        "caltech.edu:2013::CER2024:eth2_1:PWave": {
                            "port": null,
                            "uniports": {
                                "caltech.edu:2013::CER2024:eth2_1:PWave:in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:caltech-out"
                                },
                                "caltech.edu:2013::CER2024:eth2_1:PWave:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "lsanca.pacificwave.net:2016:topology:caltech-in"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "caltech.edu:2013:nsa": {
                    "services": {
                        "https://idc-v6.hep.caltech.edu:8500/ConnectionService": "NSI_CSP_2_0"
                    },
                    "name": "Caltech OSCARS uPA",
                    "type": "UPA",
                    "lat": "34.13602",
                    "lng": "-118.125404",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            }
        },
        "geant.net": {
            "nets": {
                "geant.net:2013:topology": {
                    "name": "geant.net",
                    "biports": {
                        "geant.net:2013:topology:Ubuntunet__port": {
                            "port": "Ubuntunet__port",
                            "uniports": {
                                "geant.net:2013:topology:Ubuntunet__port-in": {
                                    "type": "IN",
                                    "vlan": "1600-1610"
                                },
                                "geant.net:2013:topology:Ubuntunet__port-out": {
                                    "type": "OUT",
                                    "vlan": "1600-1610"
                                }
                            }
                        },
                        "geant.net:2013:topology:GARR__port": {
                            "port": "GARR__port",
                            "uniports": {
                                "geant.net:2013:topology:GARR__port-in": {
                                    "type": "IN",
                                    "vlan": "300-399"
                                },
                                "geant.net:2013:topology:GARR__port-out": {
                                    "type": "OUT",
                                    "vlan": "300-399"
                                }
                            }
                        },
                        "geant.net:2013:topology:GEANT-Netherlight-LON-MS-Express": {
                            "port": "GEANT-Netherlight-LON-MS-Express",
                            "uniports": {
                                "geant.net:2013:topology:GEANT-Netherlight-LON-MS-Express-in": {
                                    "type": "IN",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-lon-out"
                                },
                                "geant.net:2013:topology:GEANT-Netherlight-LON-MS-Express-out": {
                                    "type": "OUT",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-lon-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:IUCC-AP1-MS-Express-Routes": {
                            "port": "IUCC-AP1-MS-Express-Routes",
                            "uniports": {
                                "geant.net:2013:topology:IUCC-AP1-MS-Express-Routes-in": {
                                    "type": "IN",
                                    "vlan": "3750-4039"
                                },
                                "geant.net:2013:topology:IUCC-AP1-MS-Express-Routes-out": {
                                    "type": "OUT",
                                    "vlan": "3750-4039"
                                }
                            }
                        },
                        "geant.net:2013:topology:bi-southernlight": {
                            "port": "bi-southernlight",
                            "uniports": {
                                "geant.net:2013:topology:bi-southernlight-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "southernlight.net.br:2013:topology:bi-geant-out"
                                },
                                "geant.net:2013:topology:bi-southernlight-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "southernlight.net.br:2013:topology:bi-geant-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:GTS__LAB2__port__to__GEANT": {
                            "port": "GTS__LAB2__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:GTS__LAB2__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "geant.net:2013:topology:GTS__LAB2__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:funet": {
                            "port": "funet",
                            "uniports": {
                                "geant.net:2013:topology:funet-in": {
                                    "type": "IN",
                                    "vlan": "2026-2030",
                                    "aliasUrn": "funet.fi:2013:topology:espoo1-geant-out"
                                },
                                "geant.net:2013:topology:funet-out": {
                                    "type": "OUT",
                                    "vlan": "2026-2030",
                                    "aliasUrn": "funet.fi:2013:topology:espoo1-geant-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:nordunet": {
                            "port": "nordunet",
                            "uniports": {
                                "geant.net:2013:topology:nordunet-in": {
                                    "type": "IN",
                                    "vlan": "2-2014,2031-4094",
                                    "aliasUrn": "nordu.net:2013:topology:geant-out"
                                },
                                "geant.net:2013:topology:nordunet-out": {
                                    "type": "OUT",
                                    "vlan": "2-2014,2031-4094",
                                    "aliasUrn": "nordu.net:2013:topology:geant-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:GTS__LAB1__port__to__GEANT": {
                            "port": "GTS__LAB1__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:GTS__LAB1__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "geant.net:2013:topology:GTS__LAB1__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:CESNET__port": {
                            "port": "CESNET__port",
                            "uniports": {
                                "geant.net:2013:topology:CESNET__port-in": {
                                    "type": "IN",
                                    "vlan": "558-559"
                                },
                                "geant.net:2013:topology:CESNET__port-out": {
                                    "type": "OUT",
                                    "vlan": "558-559"
                                }
                            }
                        },
                        "geant.net:2013:topology:TAAS__port": {
                            "port": "TAAS__port",
                            "uniports": {
                                "geant.net:2013:topology:TAAS__port-in": {
                                    "type": "IN",
                                    "vlan": "1-3499,3500-4094"
                                },
                                "geant.net:2013:topology:TAAS__port-out": {
                                    "type": "OUT",
                                    "vlan": "1-3499,3500-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:geant-testpoint-amsterdam": {
                            "port": "geant-testpoint-amsterdam",
                            "uniports": {
                                "geant.net:2013:topology:geant-testpoint-amsterdam-in": {
                                    "type": "IN",
                                    "vlan": "2016"
                                },
                                "geant.net:2013:topology:geant-testpoint-amsterdam-out": {
                                    "type": "OUT",
                                    "vlan": "2016"
                                }
                            }
                        },
                        "geant.net:2013:topology:GTS__Prague__port__to__GEANT": {
                            "port": "GTS__Prague__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:GTS__Prague__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "geant.net:2013:topology:GTS__Prague__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:Express__server__port": {
                            "port": "Express__server__port",
                            "uniports": {
                                "geant.net:2013:topology:Express__server__port-in": {
                                    "type": "IN",
                                    "vlan": "2-4094"
                                },
                                "geant.net:2013:topology:Express__server__port-out": {
                                    "type": "OUT",
                                    "vlan": "2-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:__NetherLight__Automated__GOLE__port": {
                            "port": "__NetherLight__Automated__GOLE__port",
                            "uniports": {
                                "geant.net:2013:topology:__NetherLight__Automated__GOLE__port-in": {
                                    "type": "IN",
                                    "vlan": "4000-4039"
                                },
                                "geant.net:2013:topology:__NetherLight__Automated__GOLE__port-out": {
                                    "type": "OUT",
                                    "vlan": "4000-4039"
                                }
                            }
                        },
                        "geant.net:2013:topology:GEANT-Netherlight-AMS-MS-Express": {
                            "port": "GEANT-Netherlight-AMS-MS-Express",
                            "uniports": {
                                "geant.net:2013:topology:GEANT-Netherlight-AMS-MS-Express-in": {
                                    "type": "IN",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-ams-out"
                                },
                                "geant.net:2013:topology:GEANT-Netherlight-AMS-MS-Express-out": {
                                    "type": "OUT",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-ams-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:ExLink_0": {
                            "port": "ExLink_0",
                            "uniports": {
                                "geant.net:2013:topology:ExLink_0-in": {
                                    "type": "IN",
                                    "vlan": "1200-4094",
                                    "aliasUrn": "pionier.net.pl:2013:topology:Pionier-link-out"
                                },
                                "geant.net:2013:topology:ExLink_0-out": {
                                    "type": "OUT",
                                    "vlan": "1200-4094",
                                    "aliasUrn": "pionier.net.pl:2013:topology:Pionier-link-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:GEANT__IP__port__LON": {
                            "port": "GEANT__IP__port__LON",
                            "uniports": {
                                "geant.net:2013:topology:GEANT__IP__port__LON-in": {
                                    "type": "IN",
                                    "vlan": "2013"
                                },
                                "geant.net:2013:topology:GEANT__IP__port__LON-out": {
                                    "type": "OUT",
                                    "vlan": "2013"
                                }
                            }
                        },
                        "geant.net:2013:topology:RedIRIS__port__to__GEANT": {
                            "port": "RedIRIS__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:RedIRIS__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "2040-2042"
                                },
                                "geant.net:2013:topology:RedIRIS__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "2040-2042"
                                }
                            }
                        },
                        "geant.net:2013:topology:geant-testpoint-london": {
                            "port": "geant-testpoint-london",
                            "uniports": {
                                "geant.net:2013:topology:geant-testpoint-london-in": {
                                    "type": "IN",
                                    "vlan": "2016"
                                },
                                "geant.net:2013:topology:geant-testpoint-london-out": {
                                    "type": "OUT",
                                    "vlan": "2016"
                                }
                            }
                        },
                        "geant.net:2013:topology:geant-sinet": {
                            "port": "geant-sinet",
                            "uniports": {
                                "geant.net:2013:topology:geant-sinet-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "geant.net:2013:topology:geant-sinet-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "geant.net:2013:topology:iMinds__port__to__GEANT": {
                            "port": "iMinds__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:iMinds__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "2-4094"
                                },
                                "geant.net:2013:topology:iMinds__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "2-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:p-to-geant": {
                            "port": "p-to-geant",
                            "uniports": {
                                "geant.net:2013:topology:p-to-geant-in": {
                                    "type": "IN",
                                    "vlan": "2-4094",
                                    "aliasUrn": "ja.net:2013:topology:p-to-janet-out"
                                },
                                "geant.net:2013:topology:p-to-geant-out": {
                                    "type": "OUT",
                                    "vlan": "2-4094",
                                    "aliasUrn": "ja.net:2013:topology:p-to-janet-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:bi-geant-netherlight1": {
                            "port": "bi-geant-netherlight1",
                            "uniports": {
                                "geant.net:2013:topology:bi-geant-netherlight1-in": {
                                    "type": "IN",
                                    "vlan": "4000-4039",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-1-out"
                                },
                                "geant.net:2013:topology:bi-geant-netherlight1-out": {
                                    "type": "OUT",
                                    "vlan": "4000-4039",
                                    "aliasUrn": "netherlight.net:2013:production7:geant-1-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:deic-geant": {
                            "port": "deic-geant",
                            "uniports": {
                                "geant.net:2013:topology:deic-geant-in": {
                                    "type": "IN",
                                    "vlan": "2015-2025",
                                    "aliasUrn": "deic.dk:2013:topology:funet-geant-out"
                                },
                                "geant.net:2013:topology:deic-geant-out": {
                                    "type": "OUT",
                                    "vlan": "2015-2025",
                                    "aliasUrn": "deic.dk:2013:topology:funet-geant-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:GEANT-port-heanet": {
                            "port": "GEANT-port-heanet",
                            "uniports": {
                                "geant.net:2013:topology:GEANT-port-heanet-in": {
                                    "type": "IN",
                                    "vlan": "2-4094",
                                    "aliasUrn": "heanet.ie:2013:topology:HEANET-port-geant-out"
                                },
                                "geant.net:2013:topology:GEANT-port-heanet-out": {
                                    "type": "OUT",
                                    "vlan": "2-4094",
                                    "aliasUrn": "heanet.ie:2013:topology:HEANET-port-geant-in"
                                }
                            }
                        },
                        "geant.net:2013:topology:JANET__eMusic__Edinburgh__port": {
                            "port": "JANET__eMusic__Edinburgh__port",
                            "uniports": {
                                "geant.net:2013:topology:JANET__eMusic__Edinburgh__port-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "geant.net:2013:topology:JANET__eMusic__Edinburgh__port-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:GTS__London__port__to__GEANT": {
                            "port": "GTS__London__port__to__GEANT",
                            "uniports": {
                                "geant.net:2013:topology:GTS__London__port__to__GEANT-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "geant.net:2013:topology:GTS__London__port__to__GEANT-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        },
                        "geant.net:2013:topology:GEANT__IP__port__AMS": {
                            "port": "GEANT__IP__port__AMS",
                            "uniports": {
                                "geant.net:2013:topology:GEANT__IP__port__AMS-in": {
                                    "type": "IN",
                                    "vlan": "2017"
                                },
                                "geant.net:2013:topology:GEANT__IP__port__AMS-out": {
                                    "type": "OUT",
                                    "vlan": "2017"
                                }
                            }
                        },
                        "geant.net:2013:topology:To_Netherlight": {
                            "port": "To_Netherlight",
                            "uniports": {
                                "geant.net:2013:topology:To_Netherlight-in": {
                                    "type": "IN",
                                    "vlan": "4000-4039"
                                },
                                "geant.net:2013:topology:To_Netherlight-out": {
                                    "type": "OUT",
                                    "vlan": "4000-4039"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "geant.net:2013:nsa": {
                    "services": {
                        "https://prod-bod.geant.net:8091/nsi/ConnectionProvider": "NSI_CSP_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:geant.net:2013:topology": "NSI_TD_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:heanet.ie:2013:topology": "NSI_TD_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:ja.net:2013:topology": "NSI_TD_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:pionier.net.pl:2013:topology": "NSI_TD_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:funet.fi:2013:topology": "NSI_TD_2_0",
                        "http://bodportal.geant.net:8080/autobahn-ts/export/network/urn:ogf:network:deic.dk:2013:topology": "NSI_TD_2_0"
                    },
                    "name": "geant.net",
                    "type": "AGG",
                    "lat": "49.1",
                    "lng": "8.24",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west",
                        "nordu.net:2013:nsa",
                        "netherlight.net:2013:nsa:safnari"
                    ]
                }
            }
        },
        "manlan.internet2.edu": {
            "nsa": {
                "manlan.internet2.edu:2013:nsa": {
                    "services": {
                        "https://oscars.manlan.internet2.edu:8500/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "MANLAN OSCARS uPA",
                    "type": "UPA",
                    "lat": "40.718666",
                    "lng": "-74.003",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            },
            "nets": {
                "manlan.internet2.edu:2013:": {
                    "name": "manlan.internet2.edu",
                    "biports": {
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_6:+": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_6:+:in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:sinet_manlan"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_6:+:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "sinet.ac.jp:2013:topology:manlan_sinet"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_3:uslhcnet": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_3:uslhcnet:in": {
                                    "type": "IN",
                                    "vlan": "1800-1819,3160-3179,3400-3499"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:3_3:uslhcnet:out": {
                                    "type": "OUT",
                                    "vlan": "1800-1819,3160-3179,3400-3499"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:13_1:+": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:13_1:+:in": {
                                    "type": "IN",
                                    "vlan": "1779-1819,3400-3598,4006-4019",
                                    "aliasUrn": "netherlight.net:2013:production7:manlan:1-out"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:13_1:+:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1819,3400-3598,4006-4019",
                                    "aliasUrn": "netherlight.net:2013:production7:manlan:1-in"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_1:al2s": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_1:al2s:in": {
                                    "type": "IN",
                                    "vlan": "1125-1164,1779-1799,3200-3210,3320-3339,3400-3499,3500-3598,4006-4019",
                                    "aliasUrn": "icair.org:2013:topology:manlan-out"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_1:al2s:out": {
                                    "type": "OUT",
                                    "vlan": "1125-1164,1779-1799,3200-3210,3320-3339,3400-3499,3500-3598,4006-4019",
                                    "aliasUrn": "icair.org:2013:topology:manlan-in"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_2:esnet": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_2:esnet:in": {
                                    "type": "IN",
                                    "vlan": "1779-1799,1800-1819,3400-3499",
                                    "aliasUrn": "es.net:2013::aofa-cr5:2_1_1:manlan:out"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:15_2:esnet:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799,1800-1819,3400-3499",
                                    "aliasUrn": "es.net:2013::aofa-cr5:2_1_1:manlan:in"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_7:perfsonar": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_7:perfsonar:in": {
                                    "type": "IN",
                                    "vlan": "3400-3499"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_7:perfsonar:out": {
                                    "type": "OUT",
                                    "vlan": "3400-3499"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_6:autobahn": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_6:autobahn:in": {
                                    "type": "IN",
                                    "vlan": "3400-3499"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_6:autobahn:out": {
                                    "type": "OUT",
                                    "vlan": "3400-3499"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_1:geant-lag": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_1:geant-lag:in": {
                                    "type": "IN",
                                    "vlan": "1779-1799,3400-3499,3500-3598",
                                    "aliasUrn": "geant.net:2013:topology:manlan-out"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:1_1:geant-lag:out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799,3400-3499,3500-3598",
                                    "aliasUrn": "geant.net:2013:topology:manlan-in"
                                }
                            }
                        },
                        "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:4_3:ion": {
                            "port": null,
                            "uniports": {
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:4_3:ion:in": {
                                    "type": "IN",
                                    "vlan": "3400-3499"
                                },
                                "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:4_3:ion:out": {
                                    "type": "OUT",
                                    "vlan": "3400-3499"
                                }
                            }
                        }
                    }
                }
            }
        },
        "wix.internet2.edu": {
            "nets": {
                "wix.internet2.edu:2013:": {
                    "name": "wix.internet2.edu",
                    "biports": {
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:max100": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:max100:in": {
                                    "type": "IN",
                                    "vlan": "1860-1899"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:max100:out": {
                                    "type": "OUT",
                                    "vlan": "1860-1899"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_2:esnet": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_2:esnet:in": {
                                    "type": "IN",
                                    "vlan": "1700-1729,1860-1899,3400-3499",
                                    "aliasUrn": "es.net:2013::wash-cr5:6_1_1:wix:out"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_2:esnet:out": {
                                    "type": "OUT",
                                    "vlan": "1700-1729,1860-1899,3400-3499",
                                    "aliasUrn": "es.net:2013::wash-cr5:6_1_1:wix:in"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_1:al3s": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_1:al3s:in": {
                                    "type": "IN",
                                    "vlan": "3400-3499"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_1:al3s:out": {
                                    "type": "OUT",
                                    "vlan": "3400-3499"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxaws": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxaws:in": {
                                    "type": "IN",
                                    "vlan": "1720-1729"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxaws:out": {
                                    "type": "OUT",
                                    "vlan": "1720-1729"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:11_2:iminds": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:11_2:iminds:in": {
                                    "type": "IN",
                                    "vlan": "1165-1174"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:11_2:iminds:out": {
                                    "type": "OUT",
                                    "vlan": "1165-1174"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxdragon": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxdragon:in": {
                                    "type": "IN",
                                    "vlan": "1165-1174,1700-1719"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:15_2:maxdragon:out": {
                                    "type": "OUT",
                                    "vlan": "1165-1174,1700-1719"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:1_4:perfsonar": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:1_4:perfsonar:in": {
                                    "type": "IN",
                                    "vlan": "3400-3499"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:1_4:perfsonar:out": {
                                    "type": "OUT",
                                    "vlan": "3400-3499"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:2_2:+": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:2_2:+:in": {
                                    "type": "IN",
                                    "vlan": "1720-1729,3400-3499"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:2_2:+:out": {
                                    "type": "OUT",
                                    "vlan": "1720-1729,3400-3499"
                                }
                            }
                        },
                        "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_1:al2s": {
                            "port": null,
                            "uniports": {
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_1:al2s:in": {
                                    "type": "IN",
                                    "vlan": "1700-1729,3400-3499"
                                },
                                "wix.internet2.edu:2013::sw.net.wix.internet2.edu:13_1:al2s:out": {
                                    "type": "OUT",
                                    "vlan": "1700-1729,3400-3499"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "wix.internet2.edu:2013:nsa": {
                    "services": {
                        "https://oscars.wix.internet2.edu:8500/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "WIX OSCARS uPA",
                    "type": "UPA",
                    "lat": "38.92",
                    "lng": "-77.2116",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            }
        },
        "netherlight.net": {
            "nsa": {
                "netherlight.net:2013:nsa:safnari": {
                    "services": {
                        "https://agg.netherlight.net/dds": "NSI_DS_1_0",
                        "https://agg.netherlight.net/nsi-v2/ConnectionServiceProvider": "NSI_CSP_2_0"
                    },
                    "name": "NetherLight Safnari",
                    "type": "AGG",
                    "lat": "52.3567",
                    "lng": "4.954585",
                    "peerings": [
                        "surfnet.nl:1990:nsa:bod7",
                        "netherlight.net:2013:nsa:bod",
                        "czechlight.cesnet.cz:2013:nsa",
                        "uvalight.net:2013:nsa",
                        "icair.org:2013:nsa",
                        "es.net:2013:nsa:nsi-aggr-west",
                        "aist.go.jp:2013:nsa",
                        "southernlight.net.br:2013:nsa",
                        "nordu.net:2013:nsa",
                        "ampath.net:2013:nsa",
                        "surfnet.nl:1990:nsa:bod-acc",
                        "icair.org:2013:nsa:nsi-am-sl",
                        "geant.net:2013:nsa",
                        "twaren.nchc.org:2014:nsa",
                        "openflow.netherlight.net:2016:nsa",
                        "sinet.ac.jp:2013:nsa:nsi-aggr",
                        "lsanca.pacificwave.net:2016:nsa",
                        "sttlwa.pacificwave.net:2016:nsa",
                        "snvaca.pacificwave.net:2016:nsa",
                        "cipo.rnp.br:2014:nsa:safnari"
                    ]
                },
                "netherlight.net:2013:nsa:bod": {
                    "services": {
                        "https://bod.netherlight.net/nsi-topology/production7": "NSI_TD_2_0",
                        "https://bod.netherlight.net/nsi/v2/provider": "NSI_CSP_2_0"
                    },
                    "name": "NetherLight production",
                    "type": "UPA",
                    "lat": "52.3567",
                    "lng": "4.954585"
                }
            },
            "nets": {
                "netherlight.net:2013:production7": {
                    "name": "production7",
                    "biports": {
                        "netherlight.net:2013:production7:uva-3": {
                            "port": "Asd001A_8700_07 4/1 UvA (SNE)",
                            "uniports": {
                                "netherlight.net:2013:production7:uva-3-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "uvalight.net:2013:topology:netherlight-in"
                                },
                                "netherlight.net:2013:production7:uva-3-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "uvalight.net:2013:topology:netherlight-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:nordunet-1": {
                            "port": "Asd001A_8700_07 7/2 NORDUnet (nl-sar2-nordunet xe-0/0/3)",
                            "uniports": {
                                "netherlight.net:2013:production7:nordunet-1-out": {
                                    "type": "OUT",
                                    "vlan": "2-4095",
                                    "aliasUrn": "nordu.net:2013:topology:netherlight-in"
                                },
                                "netherlight.net:2013:production7:nordunet-1-in": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "nordu.net:2013:topology:netherlight-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:manlan:1": {
                            "port": "Asd001A_8700_07 10/1 MANLAN (via Hibernia)",
                            "uniports": {
                                "netherlight.net:2013:production7:manlan:1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1819,3400-3598,4006-4019",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:13_1:+:in"
                                },
                                "netherlight.net:2013:production7:manlan:1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1819,3400-3598,4006-4019",
                                    "aliasUrn": "manlan.internet2.edu:2013::sw.net.manlan.internet2.edu:13_1:+:out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:starlight-1": {
                            "port": "Asd001A_8700_07 8/1 StarLight/iCAIR (via GEANT Open London)",
                            "uniports": {
                                "netherlight.net:2013:production7:starlight-1-out": {
                                    "type": "OUT",
                                    "vlan": "4020-4039",
                                    "aliasUrn": "icair.org:2013:topology:netherlight-in"
                                },
                                "netherlight.net:2013:production7:starlight-1-in": {
                                    "type": "IN",
                                    "vlan": "4020-4039",
                                    "aliasUrn": "icair.org:2013:topology:netherlight-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:iperf1-2": {
                            "port": "Asd001A_8700_07 5/13 iperf1 eth2",
                            "uniports": {
                                "netherlight.net:2013:production7:iperf1-2-out": {
                                    "type": "IN",
                                    "vlan": "3000-4000",
                                    "aliasUrn": "netherlight.net:2013:production7:iperf1-2-in"
                                },
                                "netherlight.net:2013:production7:iperf1-2-in": {
                                    "type": "IN",
                                    "vlan": "3000-4000",
                                    "aliasUrn": "netherlight.net:2013:production7:iperf1-2-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:surfnet-1": {
                            "port": "Asd001A_8700_07 5/14 SURFnet (Asd001A_5410_01 5/8)",
                            "uniports": {
                                "netherlight.net:2013:production7:surfnet-1-out": {
                                    "type": "OUT",
                                    "vlan": "2-4095",
                                    "aliasUrn": "surfnet.nl:1990:production7:netherlight-1-in"
                                },
                                "netherlight.net:2013:production7:surfnet-1-in": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "surfnet.nl:1990:production7:netherlight-1-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:iperf1-3": {
                            "port": "Asd001A_8700_07 5/15 iperf1 eth3",
                            "uniports": {
                                "netherlight.net:2013:production7:iperf1-3-out": {
                                    "type": "IN",
                                    "vlan": "1700-2099",
                                    "aliasUrn": "netherlight.net:2013:production7:iperf1-3-in"
                                },
                                "netherlight.net:2013:production7:iperf1-3-in": {
                                    "type": "IN",
                                    "vlan": "1700-2099",
                                    "aliasUrn": "netherlight.net:2013:production7:iperf1-3-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:kentis-1": {
                            "port": "Asd001A_8700_07 5/16 RIX-Kentis",
                            "uniports": {
                                "netherlight.net:2013:production7:kentis-1-out": {
                                    "type": "IN",
                                    "vlan": "2050-2099",
                                    "aliasUrn": "netherlight.net:2013:production7:kentis-1-in"
                                },
                                "netherlight.net:2013:production7:kentis-1-in": {
                                    "type": "IN",
                                    "vlan": "2050-2099",
                                    "aliasUrn": "netherlight.net:2013:production7:kentis-1-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:czechlight-1": {
                            "port": "Asd001A_8700_07 5/17 CzechLight (GEANT patch panel pp-nl-e10 tray4 p5+6)",
                            "uniports": {
                                "netherlight.net:2013:production7:czechlight-1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "czechlight.cesnet.cz:2013:topology:netherlight-in"
                                },
                                "netherlight.net:2013:production7:czechlight-1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "czechlight.cesnet.cz:2013:topology:netherlight-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:esnet-1": {
                            "port": "Asd001A_8700_07 10/2 ESnet",
                            "uniports": {
                                "netherlight.net:2013:production7:esnet-1-out": {
                                    "type": "OUT",
                                    "vlan": "1000-1019",
                                    "aliasUrn": "es.net:2013::amst-cr5:3_1_1:+:in"
                                },
                                "netherlight.net:2013:production7:esnet-1-in": {
                                    "type": "IN",
                                    "vlan": "1000-1019",
                                    "aliasUrn": "es.net:2013::amst-cr5:3_1_1:+:out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:pionier-1": {
                            "port": "Asd001A_8700_07 5/18 PIONIER/PSNC (Asd001A_OME24 1/7/2 CBF to Hamburg)",
                            "uniports": {
                                "netherlight.net:2013:production7:pionier-1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "pionier.net.pl:2013:topology:netherlight-1-in"
                                },
                                "netherlight.net:2013:production7:pionier-1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "pionier.net.pl:2013:topology:netherlight-1-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:geant-1": {
                            "port": "Asd001A_8700_07 7/1 GEANT",
                            "uniports": {
                                "netherlight.net:2013:production7:geant-1-out": {
                                    "type": "OUT",
                                    "vlan": "4000-4039",
                                    "aliasUrn": "geant.net:2013:topology:bi-geant-netherlight1-in"
                                },
                                "netherlight.net:2013:production7:geant-1-in": {
                                    "type": "IN",
                                    "vlan": "4000-4039",
                                    "aliasUrn": "geant.net:2013:topology:bi-geant-netherlight1-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:geant-ams": {
                            "port": "Asd001A_8700_07 6/2 GEANT",
                            "uniports": {
                                "netherlight.net:2013:production7:geant-ams-out": {
                                    "type": "OUT",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-Netherlight-AMS-MS-Express-in"
                                },
                                "netherlight.net:2013:production7:geant-ams-in": {
                                    "type": "IN",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-Netherlight-AMS-MS-Express-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:microsoft-lon": {
                            "port": "Asd002A_5410_01 7/6 Microsoft London",
                            "uniports": {
                                "netherlight.net:2013:production7:microsoft-lon-out": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:microsoft-lon-in"
                                },
                                "netherlight.net:2013:production7:microsoft-lon-in": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:microsoft-lon-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:geant-lon": {
                            "port": "Asd002A_5410_01 7/8 GEANT",
                            "uniports": {
                                "netherlight.net:2013:production7:geant-lon-out": {
                                    "type": "OUT",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-Netherlight-LON-MS-Express-in"
                                },
                                "netherlight.net:2013:production7:geant-lon-in": {
                                    "type": "IN",
                                    "vlan": "3750-4095",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-Netherlight-LON-MS-Express-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:microsoft-ams": {
                            "port": "Asd002A_8700_05 5/5 Microsoft Amsterdam",
                            "uniports": {
                                "netherlight.net:2013:production7:microsoft-ams-out": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:microsoft-ams-in"
                                },
                                "netherlight.net:2013:production7:microsoft-ams-in": {
                                    "type": "IN",
                                    "vlan": "2-4095",
                                    "aliasUrn": "netherlight.net:2013:production7:microsoft-ams-out"
                                }
                            }
                        },
                        "netherlight.net:2013:production7:netherlight-of-1": {
                            "port": "Asd001A_8700_07 4/2 NetherLight Inventec OpenFlow",
                            "uniports": {
                                "netherlight.net:2013:production7:netherlight-of-1-out": {
                                    "type": "OUT",
                                    "vlan": "200-999,1779-1799",
                                    "aliasUrn": "openflow.netherlight.net:2016:topology:netherlight-1-in"
                                },
                                "netherlight.net:2013:production7:netherlight-of-1-in": {
                                    "type": "IN",
                                    "vlan": "200-999,1779-1799",
                                    "aliasUrn": "openflow.netherlight.net:2016:topology:netherlight-1-out"
                                }
                            }
                        }
                    }
                }
            }
        },
        "czechlight.cesnet.cz": {
            "nets": {
                "czechlight.cesnet.cz:2013:topology": {
                    "name": "czechlight.cesnet.cz:2013",
                    "biports": {
                        "czechlight.cesnet.cz:2013:topology:brno": {
                            "port": "brno",
                            "uniports": {
                                "czechlight.cesnet.cz:2013:topology:brno-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "czechlight.cesnet.cz:2013:topology:brno-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "czechlight.cesnet.cz:2013:topology:pinger": {
                            "port": "pinger",
                            "uniports": {
                                "czechlight.cesnet.cz:2013:topology:pinger-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "czechlight.cesnet.cz:2013:topology:pinger-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "czechlight.cesnet.cz:2013:topology:netherlight": {
                            "port": "netherlight",
                            "uniports": {
                                "czechlight.cesnet.cz:2013:topology:netherlight-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:czechlight-1-out"
                                },
                                "czechlight.cesnet.cz:2013:topology:netherlight-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:czechlight-1-in"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "czechlight.cesnet.cz:2013:nsa": {
                    "services": {
                        "https://opennsa.cesnet.cz:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://opennsa.cesnet.cz:9443/NSI/czechlight.cesnet.cz:2013.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "czechlight.cesnet.cz",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            }
        },
        "oess.dcn.umnet.umich.edu": {
            "nets": {
                "oess.dcn.umnet.umich.edu:2013:": {
                    "name": "oess.dcn.umnet.umich.edu",
                    "biports": {
                        "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:esnet": {
                            "port": null,
                            "uniports": {
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:esnet:in": {
                                    "type": "IN",
                                    "vlan": "3176-3179,3800-3809",
                                    "aliasUrn": "es.net:2013::star-cr5:6_2_1:umich:out"
                                },
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:esnet:out": {
                                    "type": "OUT",
                                    "vlan": "3176-3179,3800-3809",
                                    "aliasUrn": "es.net:2013::star-cr5:6_2_1:umich:in"
                                }
                            }
                        },
                        "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_1:+": {
                            "port": null,
                            "uniports": {
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_1:+:in": {
                                    "type": "IN",
                                    "vlan": "0,1-4095"
                                },
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_1:+:out": {
                                    "type": "OUT",
                                    "vlan": "0,1-4095"
                                }
                            }
                        },
                        "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:al2s": {
                            "port": null,
                            "uniports": {
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:al2s:in": {
                                    "type": "IN",
                                    "vlan": "3176-3179,3800-3809"
                                },
                                "oess.dcn.umnet.umich.edu:2013::f10-dynes.dcn.umnet.umich.edu:Te0_20:al2s:out": {
                                    "type": "OUT",
                                    "vlan": "3176-3179,3800-3809"
                                }
                            }
                        }
                    }
                }
            },
            "nsa": {
                "oess.dcn.umnet.umich.edu:2013:nsa": {
                    "services": {
                        "https://oess.dcn.umnet.umich.edu:8500/ConnectionService": "NSI_CSP_2_0"
                    },
                    "name": "Univ of Michigan OSCARS uPA",
                    "type": "UPA",
                    "lat": "42.276844",
                    "lng": "-83.73672",
                    "peerings": [
                        "es.net:2013:nsa:nsi-aggr-west"
                    ]
                }
            }
        },
        "heanet.ie": {
            "nets": {
                "heanet.ie:2013:topology": {
                    "name": "heanet.ie",
                    "biports": {
                        "heanet.ie:2013:topology:HEAnet-HRB-port": {
                            "port": "HEAnet-HRB-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEAnet-HRB-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEAnet-HRB-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:exemplar_switch_cliPort": {
                            "port": "exemplar_switch_cliPort",
                            "uniports": {
                                "heanet.ie:2013:topology:exemplar_switch_cliPort-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:exemplar_switch_cliPort-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:exemplar-port-to-heanet": {
                            "port": "exemplar-port-to-heanet",
                            "uniports": {
                                "heanet.ie:2013:topology:exemplar-port-to-heanet-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:exemplar-port-to-heanet-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-gsn-epa-port": {
                            "port": "HEANET-gsn-epa-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-gsn-epa-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEANET-gsn-epa-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-phoebus-port": {
                            "port": "HEANET-phoebus-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-phoebus-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEANET-phoebus-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:gsn_pw2_port": {
                            "port": "gsn_pw2_port",
                            "uniports": {
                                "heanet.ie:2013:topology:gsn_pw2_port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:gsn_pw2_port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-gsn-dkit-port": {
                            "port": "HEANET-gsn-dkit-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-gsn-dkit-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEANET-gsn-dkit-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-CIT-EMC-port": {
                            "port": "HEANET-CIT-EMC-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-CIT-EMC-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEANET-CIT-EMC-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:Testlab-port12": {
                            "port": "Testlab-port12",
                            "uniports": {
                                "heanet.ie:2013:topology:Testlab-port12-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:Testlab-port12-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:heanet-port-to-tssg__": {
                            "port": "heanet-port-to-tssg__",
                            "uniports": {
                                "heanet.ie:2013:topology:heanet-port-to-tssg__-in": {
                                    "type": "IN",
                                    "vlan": "2030-2034"
                                },
                                "heanet.ie:2013:topology:heanet-port-to-tssg__-out": {
                                    "type": "OUT",
                                    "vlan": "2030-2034"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:Testlab-port11": {
                            "port": "Testlab-port11",
                            "uniports": {
                                "heanet.ie:2013:topology:Testlab-port11-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:Testlab-port11-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:gd5_sw1_ge1_0_5": {
                            "port": "gd5_sw1_ge1_0_5",
                            "uniports": {
                                "heanet.ie:2013:topology:gd5_sw1_ge1_0_5-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:gd5_sw1_ge1_0_5-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-gsn-gd52-port": {
                            "port": "HEANET-gsn-gd52-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-gsn-gd52-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:HEANET-gsn-gd52-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:cpe1_gsn_port": {
                            "port": "cpe1_gsn_port",
                            "uniports": {
                                "heanet.ie:2013:topology:cpe1_gsn_port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:cpe1_gsn_port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEAnet-RIA-port": {
                            "port": "HEAnet-RIA-port",
                            "uniports": {
                                "heanet.ie:2013:topology:HEAnet-RIA-port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2005"
                                },
                                "heanet.ie:2013:topology:HEAnet-RIA-port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2005"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:Mantychore_myre_port": {
                            "port": "Mantychore_myre_port",
                            "uniports": {
                                "heanet.ie:2013:topology:Mantychore_myre_port-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050"
                                },
                                "heanet.ie:2013:topology:Mantychore_myre_port-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:tssg-port-in-blanch": {
                            "port": "tssg-port-in-blanch",
                            "uniports": {
                                "heanet.ie:2013:topology:tssg-port-in-blanch-in": {
                                    "type": "IN",
                                    "vlan": "2030-2034"
                                },
                                "heanet.ie:2013:topology:tssg-port-in-blanch-out": {
                                    "type": "OUT",
                                    "vlan": "2030-2034"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:HEANET-port-geant": {
                            "port": "HEANET-port-geant",
                            "uniports": {
                                "heanet.ie:2013:topology:HEANET-port-geant-in": {
                                    "type": "IN",
                                    "vlan": "2000-2050",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-port-heanet-out"
                                },
                                "heanet.ie:2013:topology:HEANET-port-geant-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2050",
                                    "aliasUrn": "geant.net:2013:topology:GEANT-port-heanet-in"
                                }
                            }
                        },
                        "heanet.ie:2013:topology:TSSG__XIFI__node": {
                            "port": "TSSG__XIFI__node",
                            "uniports": {
                                "heanet.ie:2013:topology:TSSG__XIFI__node-in": {
                                    "type": "IN",
                                    "vlan": "2030-2045"
                                },
                                "heanet.ie:2013:topology:TSSG__XIFI__node-out": {
                                    "type": "OUT",
                                    "vlan": "2030-2045"
                                }
                            }
                        }
                    }
                }
            }
        },
        "twaren.nchc.org": {
            "nsa": {
                "twaren.nchc.org:2014:nsa": {
                    "services": {
                        "https://nsi.twaren.net:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://nsi.twaren.net:9443/NSI/twaren.nchc.org:2014.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "twaren.nchc.org",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            },
            "nets": {
                "twaren.nchc.org:2014:topology": {
                    "name": "twaren.nchc.org:2014",
                    "biports": {
                        "twaren.nchc.org:2014:topology:ofport2": {
                            "port": "ofport2",
                            "uniports": {
                                "twaren.nchc.org:2014:topology:ofport2-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "twaren.nchc.org:2014:topology:ofport2-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "twaren.nchc.org:2014:topology:ofport3": {
                            "port": "ofport3",
                            "uniports": {
                                "twaren.nchc.org:2014:topology:ofport3-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "icair.org:2013:topology:twanetout"
                                },
                                "twaren.nchc.org:2014:topology:ofport3-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "icair.org:2013:topology:twanetin"
                                }
                            }
                        },
                        "twaren.nchc.org:2014:topology:ofport4": {
                            "port": "ofport4",
                            "uniports": {
                                "twaren.nchc.org:2014:topology:ofport4-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "twaren.nchc.org:2014:topology:ofport4-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        }
                    }
                }
            }
        },
        "uvalight.net": {
            "nsa": {
                "uvalight.net:2013:nsa": {
                    "services": {
                        "https://nsa.uvalight.net:9443/NSI/services/CS2": "NSI_CSP_2_0",
                        "https://nsa.uvalight.net:9443/NSI/uvalight.net:2013.nml.xml": "NSI_TD_2_0"
                    },
                    "name": "uvalight.net",
                    "type": "AGG",
                    "lat": null,
                    "lng": null
                }
            },
            "nets": {
                "uvalight.net:2013:topology": {
                    "name": "uvalight.net:2013",
                    "biports": {
                        "uvalight.net:2013:topology:dmz": {
                            "port": "dmz",
                            "uniports": {
                                "uvalight.net:2013:topology:dmz-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799"
                                },
                                "uvalight.net:2013:topology:dmz-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799"
                                }
                            }
                        },
                        "uvalight.net:2013:topology:exogeni": {
                            "port": "exogeni",
                            "uniports": {
                                "uvalight.net:2013:topology:exogeni-in": {
                                    "type": "IN",
                                    "vlan": "1779-1800"
                                },
                                "uvalight.net:2013:topology:exogeni-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1800"
                                }
                            }
                        },
                        "uvalight.net:2013:topology:netherlight": {
                            "port": "netherlight",
                            "uniports": {
                                "uvalight.net:2013:topology:netherlight-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:uva-3-out"
                                },
                                "uvalight.net:2013:topology:netherlight-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:uva-3-in"
                                }
                            }
                        }
                    }
                }
            }
        },
        "deic.dk": {
            "nets": {
                "deic.dk:2013:topology": {
                    "name": "deic.dk",
                    "biports": {
                        "deic.dk:2013:topology:funet-geant": {
                            "port": "funet-geant",
                            "uniports": {
                                "deic.dk:2013:topology:funet-geant-in": {
                                    "type": "IN",
                                    "vlan": "2015-2025",
                                    "aliasUrn": "geant.net:2013:topology:deic-geant-out"
                                },
                                "deic.dk:2013:topology:funet-geant-out": {
                                    "type": "OUT",
                                    "vlan": "2015-2025",
                                    "aliasUrn": "geant.net:2013:topology:deic-geant-in"
                                }
                            }
                        },
                        "deic.dk:2013:topology:StoragePort": {
                            "port": "StoragePort",
                            "uniports": {
                                "deic.dk:2013:topology:StoragePort-in": {
                                    "type": "IN",
                                    "vlan": "2015-2025"
                                },
                                "deic.dk:2013:topology:StoragePort-out": {
                                    "type": "OUT",
                                    "vlan": "2015-2025"
                                }
                            }
                        },
                        "deic.dk:2013:topology:iperfPort": {
                            "port": "iperfPort",
                            "uniports": {
                                "deic.dk:2013:topology:iperfPort-in": {
                                    "type": "IN",
                                    "vlan": "1-4094"
                                },
                                "deic.dk:2013:topology:iperfPort-out": {
                                    "type": "OUT",
                                    "vlan": "1-4094"
                                }
                            }
                        }
                    }
                }
            }
        },
        "ja.net": {
            "nets": {
                "ja.net:2013:topology": {
                    "name": "ja.net",
                    "biports": {
                        "ja.net:2013:topology:bonfire-1": {
                            "port": "bonfire-1",
                            "uniports": {
                                "ja.net:2013:topology:bonfire-1-in": {
                                    "type": "IN",
                                    "vlan": "940-951"
                                },
                                "ja.net:2013:topology:bonfire-1-out": {
                                    "type": "OUT",
                                    "vlan": "940-951"
                                }
                            }
                        },
                        "ja.net:2013:topology:caliban-ethfib": {
                            "port": "caliban-ethfib",
                            "uniports": {
                                "ja.net:2013:topology:caliban-ethfib-in": {
                                    "type": "IN",
                                    "vlan": "2003-2020"
                                },
                                "ja.net:2013:topology:caliban-ethfib-out": {
                                    "type": "OUT",
                                    "vlan": "2003-2020"
                                }
                            }
                        },
                        "ja.net:2013:topology:p-to-janet": {
                            "port": "p-to-janet",
                            "uniports": {
                                "ja.net:2013:topology:p-to-janet-in": {
                                    "type": "IN",
                                    "vlan": "2003-2020",
                                    "aliasUrn": "geant.net:2013:topology:p-to-geant-out"
                                },
                                "ja.net:2013:topology:p-to-janet-out": {
                                    "type": "OUT",
                                    "vlan": "2003-2020",
                                    "aliasUrn": "geant.net:2013:topology:p-to-geant-in"
                                }
                            }
                        },
                        "ja.net:2013:topology:ganymede-ethfib": {
                            "port": "ganymede-ethfib",
                            "uniports": {
                                "ja.net:2013:topology:ganymede-ethfib-in": {
                                    "type": "IN",
                                    "vlan": "2003-2020"
                                },
                                "ja.net:2013:topology:ganymede-ethfib-out": {
                                    "type": "OUT",
                                    "vlan": "2003-2020"
                                }
                            }
                        },
                        "ja.net:2013:topology:ge-1__0__1": {
                            "port": "ge-1__0__1",
                            "uniports": {
                                "ja.net:2013:topology:ge-1__0__1-in": {
                                    "type": "IN",
                                    "vlan": "2003-2022"
                                },
                                "ja.net:2013:topology:ge-1__0__1-out": {
                                    "type": "OUT",
                                    "vlan": "2003-2022"
                                }
                            }
                        },
                        "ja.net:2013:topology:ge-1__1__5": {
                            "port": "ge-1__1__5",
                            "uniports": {
                                "ja.net:2013:topology:ge-1__1__5-in": {
                                    "type": "IN",
                                    "vlan": "2003-2022"
                                },
                                "ja.net:2013:topology:ge-1__1__5-out": {
                                    "type": "OUT",
                                    "vlan": "2003-2022"
                                }
                            }
                        }
                    }
                }
            }
        },
        "pionier.net.pl": {
            "nets": {
                "pionier.net.pl:2013:topology": {
                    "name": "pionier.net.pl",
                    "biports": {
                        "pionier.net.pl:2013:topology:PORT_TO_PIONIER": {
                            "port": "PORT_TO_PIONIER",
                            "uniports": {
                                "pionier.net.pl:2013:topology:PORT_TO_PIONIER-in": {
                                    "type": "IN",
                                    "vlan": "2000-2010"
                                },
                                "pionier.net.pl:2013:topology:PORT_TO_PIONIER-out": {
                                    "type": "OUT",
                                    "vlan": "2000-2010"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:felix-ge-1-0-9": {
                            "port": "felix-ge-1-0-9",
                            "uniports": {
                                "pionier.net.pl:2013:topology:felix-ge-1-0-9-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:felix-ge-1-0-9-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:host2": {
                            "port": "host2",
                            "uniports": {
                                "pionier.net.pl:2013:topology:host2-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:host2-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:felix-to-i2cat": {
                            "port": "felix-to-i2cat",
                            "uniports": {
                                "pionier.net.pl:2013:topology:felix-to-i2cat-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:felix-to-i2cat-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:GEANT_10G_PORT_TO_PIONIER": {
                            "port": "GEANT_10G_PORT_TO_PIONIER",
                            "uniports": {
                                "pionier.net.pl:2013:topology:GEANT_10G_PORT_TO_PIONIER-in": {
                                    "type": "IN",
                                    "vlan": "1200-2000",
                                    "aliasUrn": "geant.net:2013:topology:ExLink_0-out"
                                },
                                "pionier.net.pl:2013:topology:GEANT_10G_PORT_TO_PIONIER-out": {
                                    "type": "OUT",
                                    "vlan": "1200-2000",
                                    "aliasUrn": "geant.net:2013:topology:ExLink_0-in"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:netherlight-1": {
                            "port": "netherlight-1",
                            "uniports": {
                                "pionier.net.pl:2013:topology:netherlight-1-in": {
                                    "type": "IN",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:pionier-1-out"
                                },
                                "pionier.net.pl:2013:topology:netherlight-1-out": {
                                    "type": "OUT",
                                    "vlan": "1779-1799",
                                    "aliasUrn": "netherlight.net:2013:production7:pionier-1-in"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:felix-ge-1-0-3": {
                            "port": "felix-ge-1-0-3",
                            "uniports": {
                                "pionier.net.pl:2013:topology:felix-ge-1-0-3-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:felix-ge-1-0-3-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:felix-ge-1-1-7": {
                            "port": "felix-ge-1-1-7",
                            "uniports": {
                                "pionier.net.pl:2013:topology:felix-ge-1-1-7-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:felix-ge-1-1-7-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:bonfire_port__": {
                            "port": "bonfire_port__",
                            "uniports": {
                                "pionier.net.pl:2013:topology:bonfire_port__-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:bonfire_port__-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:MDVPN__lab__port": {
                            "port": "MDVPN__lab__port",
                            "uniports": {
                                "pionier.net.pl:2013:topology:MDVPN__lab__port-in": {
                                    "type": "IN",
                                    "vlan": "4-4095"
                                },
                                "pionier.net.pl:2013:topology:MDVPN__lab__port-out": {
                                    "type": "OUT",
                                    "vlan": "4-4095"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:server_port": {
                            "port": "server_port",
                            "uniports": {
                                "pionier.net.pl:2013:topology:server_port-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:server_port-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        },
                        "pionier.net.pl:2013:topology:felix-ge-1-0-4": {
                            "port": "felix-ge-1-0-4",
                            "uniports": {
                                "pionier.net.pl:2013:topology:felix-ge-1-0-4-in": {
                                    "type": "IN",
                                    "vlan": "1-4096"
                                },
                                "pionier.net.pl:2013:topology:felix-ge-1-0-4-out": {
                                    "type": "OUT",
                                    "vlan": "1-4096"
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}';

        $parser = new NSIParser();
        $parser->loadFile('https://agg.cipo.rnp.br/dds');
        $parser->parseTopology();
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $parser->getData();


    }
    
    public function actionRequest() {
        $form = new ReservationForm;
        if ($form->load($_POST)) {
            
            //Confere se usuário tem permissão para reservas na origem OU no destino
            /*$source = Port::findOne(['id' => $form->src_port]);
            $destination = Port::findOne(['id' => $form->dst_port]);
            $permission = false;
            if($source){
                $source = $source->getDevice()->one();
                if($source){
                    $domain = $source->getDomain()->one();
                    if($domain && self::can('reservation/create', $domain->name)) $permission = true;
                }
            }
            if($destination){
                $destination = $destination->getDevice()->one();
                if($destination){
                    $domain = $destination->getDomain()->one();
                    if($domain &&self::can('reservation/create', $domain->name)) $permission = true;
                }
            }
            if(!$permission){ //Se ele não tiver em nenhum dos dois, exibe aviso
                return -1;
            }*/

            if ($form->save()) {
                Yii::$app->getSession()->addFlash('success', Yii::t('circuits', 'Circuit reservation successfully registered. Please wait while we check for required resources.'));
                return $form->reservation->id;
            }
        }

        return $this->redirect("create");
    }
    
    public function actionConfirm() {
        self::beginAsyncAction();
        
        $reservation = Reservation::findOne($_POST['id']);
        $reservation->confirm();

        return "";
    }
    
    //Verificar, pois a cada atualizacao da pagina ele vai verificar as autorizações, 
    //isso está fora do contexto dessa função. Deveria ser feito por workflows.
    public function actionView($id) {
        $reservation = Reservation::findOne($id);
        $totalConns = $reservation->getConnections()->count();
        Yii::trace($totalConns);
        if ($totalConns == 1) {
            return $this->redirect(['/circuits','id'=>$reservation->
                getConnections()->
                select(['id'])->
                asArray()->
                one()['id']]);
        }
        
        //Confere se algum pedido de autorização da expirou
        /*
        if($reservation){
            $connectionsExpired = $conn = Connection::find()->where(['reservation_id' => $reservation->id])->andWhere(['<=','start', DateUtils::now()])->all();
            foreach($connectionsExpired as $connection){
                $requests = ConnectionAuth::find()->where(['connection_id' => $connection->id, 'status' => Connection::AUTH_STATUS_PENDING])->all();
                foreach($requests as $request){
                    $request->changeStatusToExpired();
                    $connection->auth_status= Connection::AUTH_STATUS_EXPIRED;
                    $connection->save();
                    Notification::createConnectionNotification($connection->id);
                }
            }
        }

        //Confere a permissão
        $domains_name = [];
        foreach(self::whichDomainsCan('reservation/read') as $domain) $domains_name[] = $domain->name;
        $permission = false;
        if(Yii::$app->user->getId() == $reservation->request_user_id) $permission = true; //Se é quem requisitou
        else {
            $conns = Connection::find()->where(['reservation_id' => $reservation->id])->select(["id"])->all();
            if(!empty($conns)){
                $conn_ids = [];
                foreach($conns as $conn) $conn_ids[] = $conn->id;
            
                $paths = ConnectionPath::find()
                         ->where(['in', 'domain', $domains_name])
                         ->andWhere(['in', 'conn_id', $conn_ids])
                         ->select(["conn_id"])->distinct(true)->one();
                 
                if(!empty($paths)) $permission = true;
            }
        }
        
        if(!$permission){ //Se ele não tiver permissão em nenhum domínio do path e não for quem requisitou
            return $this->goHome();
        }*/
        
        $connDataProvider = new ActiveDataProvider([
                'query' => $reservation->getConnections(),
                'sort' => false,
                'pagination' => [
                    'pageSize' => 5,
                ]
        ]);

        return $this->render('view/view',[
                'reservation' => $reservation,
                'connDataProvider' => $connDataProvider
        ]);
    }

    public function actionStatus() {
        $searchModel = new ReservationSearch;
        $allowedDomains = self::whichDomainsCan('reservation/read', true);

        $data = $searchModel
            ->searchByDomains(Yii::$app->request->get(), $allowedDomains);

        if(Yii::$app->request->isPjax) {
            switch ($_GET['_pjax']) {
                case '#circuits-pjax':
                    return $this->renderAjax('status/_grid', [
                        'gridId' => 'circuits-grid',
                        'searchModel' => $searchModel, 
                        'data' => $data,
                        'allowedDomains' => $allowedDomains
                    ]);
            }
        }
        
        //deve ser feito quando ha duas ou mais grids na mesma pagina   
        //$scheduledData->pagination->pageParam = 'scheduled-page';
        //$finishedData->pagination->pageParam = 'finished-page';

        return $this->render('status/status', [
            'searchModel' => $searchModel,
            'data' => $data,
            'allowedDomains' => $allowedDomains
        ]);
    }
    
    //////REST functions

    public function actionGetPortByDevice($id, $cols=null) {
        $query = Port::find()->where(['device_id'=>$id])->orderBy(['name'=>'SORT ASC'])->asArray();

        if (!CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_UNIPORT_ENABLED)->getBoolean()) {
            $query->andWhere(['directionality'=>Port::DIR_BI]);
        }

        if (CircuitsPreference::findOne(CircuitsPreference::CIRCUITS_PROTOCOL)->value == Service::TYPE_NSI_CSP_2_0) {
            $query->andWhere(['type'=>Port::TYPE_NSI]);
        }

        $cols ? $data = $query->select(json_decode($cols))->all() : $data = $query->all();

        $temp = Json::encode($data);
        Yii::trace($temp);
        return $temp;
    }
}