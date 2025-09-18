import { useAppConfig } from "@/contexts/AppConfigContext";

import React from 'react'

const tabsConfig = () => {
  const {useDictionary} = useAppConfig();
  const dictionary = useDictionary();
  const item_settings_tabs = [
  { key: 'itemPriorityRules', label: dictionary?.settings?.itemPriorityRulesManagement?.tab?.itemPriorityRules, path: "/warehouse/settings/item-priority-rules" },
];
return item_settings_tabs;
}
export default tabsConfig
