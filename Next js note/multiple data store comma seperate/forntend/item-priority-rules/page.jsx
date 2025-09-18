"use client";
import { Grid } from '@mui/material';
import React, { useState } from "react";
import {
  BUTTON_ACTION_TYPE_DELETE,
  BUTTON_ACTION_TYPE_EDIT,
  BUTTON_ACTION_TYPE_DETAILS
} from "@/constants/static-const";

import { useRouter } from "next/navigation";
import { useAppConfig } from '@/contexts/AppConfigContext';
import AddButton from "@/components/Buttons/AddButton";
import PageTitle from "@/components/pages/PageTitle";
import useCrudService from '@/hooks/useCrudService';
import { HTTP_OK, HTTP_UNPROCESSABLE_ENTITY } from '@/constants/HttpStatusCode';
import { ERROR, notify, SUCCESS, WARNING } from '@/utils/helpers';
import {postData} from "@/app/(smart-office)/(custom-fields)/services/apiService";
import {ITEM_PRIORITY_RULES_API_ENDPOINTS} from '../SettingsApiEndPoints';
import useFilter from '@/hooks/useFilter';
import DeleteDialog from '@/components/DialogBox/DeleteDialog';
import ItemPriorityRulesList from "./_components/ItemPriorityRulesList";
import {SETTING_MODULES} from "@/app/warehouse/constants/routes";
import tabsConfig from "./tabsConfig";
import GlobalTabs from '@/components/Stepper/GlobalTabs';
import Details from "./_components/Details";
import apiClient from "@/lib/axios";

/**
 * Default attributes to be fetched from the API
 * @type {string[]}
 */
const defaultAttributes = ["id", "rule"];
/**
 * Default columns to be displayed in the table
 * @type {string[]}
 */
const selectableColumns = [
  "rule",
  "location_id",
  "item_category_id",
  "row_status"
]

const ItemPriorityRules = () => {
  const [activeMenu, setActiveMenu] = useState("locationType");
  const router                      = useRouter();
  const {useDictionary,config}                        = useAppConfig()
  const dictionary                                    = useDictionary();
  const settings_tabs = tabsConfig();
  const [mutation, setMutation]                       = useState(false);
  const [itemID, setItemID]                           = useState(null);
  const [editData, setEditData]                       = useState([]);
  const [deleteID, setDeleteID] = useState([]);
  const [selectedRows, setSelectedRows] = useState([]);
  const [table, setTable] = useState(null);
  const [deleteDialogOpen, setDeleteDialogOpen]       = useState(false);
  const [detailsModal, setDetailsModal]               = useState(false);
  const [detailInfo, setDetailsInfo] = useState({});
  const locationDictionary = dictionary?.settings?.itemPriorityRulesManagement;
  const {filter, setFilter} = useFilter({
    fields: {
      default: defaultAttributes,
      custom: selectableColumns
    }
  })
  const {
      data : supplierItem,
      isLoading,
      destroy, 
      mutate
    } = useCrudService(
      ITEM_PRIORITY_RULES_API_ENDPOINTS.ITEM_PRIORITY_RULES_LIST,
      {
        params: filter,
      }
  );
  const onBulkDeleteClick = (selectedRows, tableInstance) => {
    const ids = selectedRows?.map((row) => row?.id);
    setDeleteID(ids);
    setDeleteDialogOpen(true);
    setTable(tableInstance);
  }
  const handleDeleteConfirm = async () => {
      setDeleteDialogOpen(false);
      await handleDelete(deleteID);
  };

  const handleDelete = async (ids) => {
      try {
          const deleteResponse = await postData(
              ITEM_PRIORITY_RULES_API_ENDPOINTS?.ITEM_PRIORITY_RULES_BULK_DELETE,
              {
                  ids: ids
              }
          );
          if ([HTTP_OK].includes(deleteResponse.status)) {
              notify('Item Priority Rules Deleted Successfully!', SUCCESS);
              setSelectedRows([]);
              setDeleteDialogOpen(false);
              if (table) {
                  table.resetRowSelection();
              }
              await mutate();
          }
      } catch (error) {
          notify(error?.message, ERROR);
          return false;
      }
  };
  const handleChange = (event, newValue) => {
    setActiveMenu(newValue);
    router.push(settings_tabs.find((tab) => tab.key === newValue).path);
  };

  const fetchdetailInformation = async (params) => {
      try {
        const responseData = await apiClient.get(ITEM_PRIORITY_RULES_API_ENDPOINTS.ITEM_PRIORITY_RULES_UPDATE + '/' + params.id );
        if (responseData?.status == HTTP_OK) {
            setDetailsModal(true);
            setDetailsInfo({
                data: responseData?.data
            });
        }
    } catch (error) {
        notify(error?.message, ERROR);
    }
  };
  const handleAction = async (action, row) => {
    switch (action) {
      case BUTTON_ACTION_TYPE_EDIT:
        setEditData(row);
        const url = `${SETTING_MODULES.ITEM_PRIORITY_RULES_UPDATE}/${row.id}`;
        return router.push(url);
        break;
      case BUTTON_ACTION_TYPE_DETAILS:
         await fetchdetailInformation(
          {
            id: row.id,
          }
        );
        break;
      case BUTTON_ACTION_TYPE_DELETE:
        const idData = [row.id]
        setDeleteID(idData);
        setDeleteDialogOpen(true);
        break;
      default:
        notify('Something went wrongs!', ERROR);
        console.warn("Unhandled action:", action);
    }
  };

  const handleAddSupplierClick = () => {
    router.push(SETTING_MODULES.ITEM_PRIORITY_RULES_CREATE);
  };
  const handleBulkDelete = (selectedRows, tableInstance) => {
      const ids = selectedRows?.map((row) => row?.id);
      setDeleteID(ids);
      setDeleteDialogOpen(true);
      setTable(tableInstance);
  }

  const actionButton = (
      <>
        <AddButton
            variant="contained"
            color="primary"
            onClick={handleAddSupplierClick}
        >
          {locationDictionary?.addTitle}
        </AddButton>
      </>
  );


  return (
      <Grid container spacing={2}>
        <Grid item xs={12}>
          <PageTitle title={locationDictionary?.title} />
        </Grid>
        <Grid item xs={12}>
        <GlobalTabs
          tabs={settings_tabs}
          handleChange={handleChange}
          value={activeMenu}
        />
      </Grid>
        <Grid item xs={12}>
          <ItemPriorityRulesList
              data={supplierItem ?? {}}
              handleAction={handleAction}
              loading={isLoading}
              actionButton={actionButton}
              advanceFilter={true}
              defaultColumns={[
                ...(filter?.fields?.split(",") || [])
              ]}
              docType="warehouse_setting_location_type"
              onBulkDeleteClick={onBulkDeleteClick}
          />

          <DeleteDialog
              title={locationDictionary?.confirmDialogTitle}
              message={locationDictionary?.confirmSubTitle}
              open={deleteDialogOpen}
              onClose={() => setDeleteDialogOpen(false)}
              onConfirm={handleDeleteConfirm}
          />
          <Grid xs={12} item>
            <Details
              open={detailsModal}
              onClose={() => setDetailsModal(false)}
              data={detailInfo}
            />
        </Grid>
        </Grid>
      </Grid>
  );
};

export default ItemPriorityRules;
