import React, { useMemo, useState } from 'react';
import DataTableComponent from '@/components/Tables/DataTableComponent';
import {
  Chip,
  useTheme
} from "@mui/material";
import { useAppConfig } from "@/contexts/AppConfigContext";
import {
  ROW_STATUS_ACTIVE,
  ROW_STATUS_INACTIVE, COMMON_BUTTON_ACTION_VIEW_DELETE
} from '@/constants/static-const';
import { ADVANCE_FILTER_FIELD_TYPE_SINGLE_SELECT, ADVANCE_FILTER_FIELD_TYPE_TEXT } from "@/constants/table-filter";

const ItemPriorityRulesList = ({ data, handleAction, loading, actionButton, advanceFilter, defaultColumns, docType, onBulkDeleteClick }) => {
  const theme = useTheme();
  const { useDictionary } = useAppConfig();
  const dictionary = useDictionary();
  const RULE_OPTIONS = {
    1: "First in First Out (FIFO)",
    2: "Last in First Out (LIFO)",
    3: "First Expired First Out (FEFO)"
  };

  console.log('api', data);

  const ROW_STATUS_OPTIONS = {
    0: { label: ROW_STATUS_INACTIVE, color: theme.palette.common, bg: theme.palette.neutral.light },
    1: { label: ROW_STATUS_ACTIVE, color: theme.palette.success.main, bg: theme.palette.success.lighter }
  };

  const columns = useMemo(
    () => [
      {
        id: "rule",
        accessorKey: "rule",
        header: "Rule",
        size: 200,
        filterable: true,
        filterType: "text",
        columnFilter: true,
        columnOrder: true,
        advanceFilter: {
          fieldType: ADVANCE_FILTER_FIELD_TYPE_TEXT
        },
        Cell: ({ row }) => {
          const ruleValue = row.original.rule;
          const ruleLabel = RULE_OPTIONS[ruleValue] || "";
          return <span>{ruleLabel}</span>;
        }
      },
      {
        id: "location_id",
        accessorKey: "locations", // Use locations array to display names
        header: "Location",
        size: 100,
        filterable: true,
        filterType: "text",
        columnFilter: true,
        columnOrder: true,
        advanceFilter: {
          fieldType: ADVANCE_FILTER_FIELD_TYPE_TEXT
        },
        Cell: ({ row }) => {
          // Handle multiple locations
          const locations = row.original.locations || [];
          const locationNames = locations.map(location => location.name).join(", "); // Join names with commas
          return <span>{locationNames}</span>;
        }
      },
      {
        id: "item_category_id",
        accessorKey: "item_categories", // Use item_categories array to display names
        header: "Item Category",
        size: 100,
        filterable: true,
        filterType: "text",
        columnFilter: true,
        columnOrder: true,
        advanceFilter: {
          fieldType: ADVANCE_FILTER_FIELD_TYPE_TEXT
        },
        Cell: ({ row }) => {
          // Handle multiple item categories
          const categories = row.original.item_categories || [];
          const categoryNames = categories.map(category => category.name).join(", "); // Join names with commas
          return <span>{categoryNames}</span>;
        }
      },
      {
        accessorKey: "row_status",
        header: "Status",
        Cell: ({ row }) => {
          const status = row.original.row_status;
          const statusData = ROW_STATUS_OPTIONS[status];
          const label = statusData?.label || 'Unknown';
          const color = statusData?.color || 'default';
          const bg = statusData?.bg || 'transparent';
          return (
            <Chip
              label={label}
              size='small'
              sx={{ backgroundColor: bg, color: color, fontWeight: 700 }}
            />
          );
        },
        size: 100,
        columnFilter: true,
        columnOrder: true
      }
    ],
    [data, handleAction]
  );

  return (
    <div>
      <DataTableComponent
        data={data}
        columns={columns}
        defaultColumns={defaultColumns}
        loading={loading}
        actionButton={actionButton}
        advanceFilter={advanceFilter}
        isSelectable={true}
        actionMenuItems={COMMON_BUTTON_ACTION_VIEW_DELETE.map((item) => ({
          ...item,
          handleAction: handleAction
        }))}
        onBulkDeleteClick={onBulkDeleteClick}
        docType={docType}
      />
    </div>
  );
}

export default ItemPriorityRulesList;
