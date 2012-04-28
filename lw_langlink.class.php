<?php

/* * ************************************************************************
 *  Copyright notice
 *
 *  Copyright 2011-2012 Logic Works GmbH
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *  http://www.apache.org/licenses/LICENSE-2.0
 *  
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *  
 * ************************************************************************* */

// [PLUGIN:lw_langlink?develop_only=1&template=85]

class lw_langlink extends lw_plugin {

    function __construct() 
    {
        parent::__construct();
        $this->page = lw_page::getInstance($this->request->getIndex());
        $reg 	 	= lw_registry::getInstance();
    	$this->auth = $reg->getEntry("auth");
    }
    
    function buildPageOutput() 
    {
        if ($this->params['develop_only'] == 1 && $this->auth->isLoggedIn()) {
            $this->db->setStatement("SELECT * FROM t:lw_page_langlink WHERE page_link > 0 AND page_id = :id ");
            $this->db->bindParameter('id', 'i', $this->request->getIndex());
            $erg = $this->db->pselect();

            if (intval($this->params['template'])>0) {
                $this->db->setStatement("SELECT * FROM t:lw_templates WHERE id = :id ");
                $this->db->bindParameter('id', 'i', $this->params['template']);
                $dummy = $this->db->pselect1();
                $tpla   = $dummy['template'];
            }
            
            if (!$tpla) {
                $tpla = '<!-- lw:blockstart default --><div class="lw_lang_link"><a href="<!-- lw:var link -->"><!-- lw:var shortcut --></a></div><!-- lw:blockend default -->';
            }
            $blocks = new lw_te($tpla);
            if (is_array($erg)) {
                foreach($erg as $lang) {
                    if ($lang['page_link'] > 0 && strlen($lang['language']) > 0) {
                        
                        $tpld   = $blocks->getBlock(strtolower($lang['language']));
                        if (!$tpld) {
                            $tpld   = $blocks->getBlock('default');
                        }
                        $tpl    = new lw_te($tpld);                   
                        $tpl->reg('link', lw_url::get(array('index'=>$lang['page_link'])));
                        $tpl->reg('shortcut', $lang['language']);
                        $link.= $tpl->parse();
                    }
                }
                return $link;
            }
        }
    }
}
