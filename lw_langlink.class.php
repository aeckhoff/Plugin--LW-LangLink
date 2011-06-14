<?php
<?php

// [PLUGIN:lw_langlink?develop_only=1&template=85]

class lw_langlink extends lw_plugin {

    function __construct() {
        parent::__construct();
        $this->page = lw_page::getInstance($this->request->getIndex());
        $reg 	 	= lw_registry::getInstance();
    	$this->auth = $reg->getEntry("auth");
    }
    
    function buildPageOutput() {
        if ($this->params['develop_only'] == 1 && $this->auth->isLoggedIn()) {
            $sql = "SELECT * FROM ".$this->db->gt('lw_page_langlink')." WHERE page_link > 0 AND page_id = ".$this->request->getIndex();
            $erg = $this->db->select($sql);

            if (intval($this->params['template'])>0) {
                $sql   = "SELECT * FROM ".$this->db->gt('lw_templates')." WHERE id = ".intval($this->params['template']);
                $dummy = $this->db->select1($sql);
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