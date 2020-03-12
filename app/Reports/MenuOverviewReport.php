<?php namespace App\Reports;

use Illuminate\Support\Facades\DB;

class MenuOverviewReport {
    const CONNECTION = 'reports_db';

    protected $response;

    protected function menuTypeMenuItemReport() {
        $this->response['menuTypeMenuItemReport'] = DB::connection(MenuOverviewReport::CONNECTION)->select( DB::raw("SELECT  DISTINCT  MT.menu_type_name, MT.active AS Menu_Type_Active,  I.item_name, I.description, I.active AS Item_Active
		FROM Item I
		JOIN Menu_Type MT ON MT.menu_type_id = I.menu_type_id
		JOIN Merchant_Menu_Map MMM ON MMM.menu_id = MT.menu_id
		WHERE MMM.menu_id = :menu_id 
		ORDER BY  MT.priority DESC, I.priority DESC"),

            ['menu_id'=>session('current_menu_id')]
        );
    }

    protected function modifierGroupModifierItemReport() {
        $this->response['modifierGroupModifierItemReport'] = DB::connection(MenuOverviewReport::CONNECTION)->select( DB::raw('SELECT  DISTINCT  MG.modifier_group_name, MG.active as MG_active, MI.modifier_item_name, 
                        CASE WHEN MI.logical_delete = "Y" THEN "N"  WHEN MI.logical_delete = "N" THEN "Y" ELSE "0" END AS MI_active 
                        FROM Modifier_Item MI
                        JOIN Modifier_Group MG ON MG.modifier_group_id = MI.modifier_group_id
                        JOIN Merchant_Menu_Map MMM ON MMM.menu_id = MG.menu_id
                        WHERE MMM.menu_id = :menu_id
                        ORDER BY  MG.priority DESC,  MI.priority DESC'),

            ['menu_id'=>session('current_menu_id')]
        );
    }

    protected function merchantLevelReport($merchant_id) {
        $this->response['merchantMenuReport'] = DB::connection(MenuOverviewReport::CONNECTION)->select( DB::raw('SELECT  DISTINCT S.map_id AS imgm_id, MT.menu_type_id AS mt_id, MT.priority AS mt_priority, MT.menu_type_name, I.item_id AS item_id, I.priority AS i_priority,  I.item_name, S.priority AS mg_priority, M.modifier_group_id AS mg_id, M.modifier_group_name
                FROM Item_Modifier_Group_Map S
                JOIN Item I ON I.item_id = S.item_id
                JOIN Menu_Type MT ON MT.menu_type_id = I.menu_type_id
                JOIN Merchant_Menu_Map MMM ON MMM.menu_id = MT.menu_id
                JOIN Modifier_Group M ON M.modifier_group_id = S.modifier_group_id
                WHERE MMM.menu_id = :menu_id
                AND S.merchant_id = :merchant_id
                AND S.logical_delete = "N"
                ORDER BY  MT.priority DESC, MT.menu_type_name ASC,  I.priority DESC, I.item_name ASC, S.priority DESC'),

            ['menu_id'=>session('current_menu_id'), 'merchant_id'=>$merchant_id]
        );
    }

    public function buildReport($merchant_id) {
        $this->menuTypeMenuItemReport();
        $this->modifierGroupModifierItemReport();
        $this->merchantLevelReport($merchant_id);
        return $this->response;
    }
}