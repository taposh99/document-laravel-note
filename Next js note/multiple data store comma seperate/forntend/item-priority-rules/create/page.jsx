'use client';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { Formik, Form } from 'formik';
import { Box, Button, Grid, useTheme } from '@mui/material';
import * as Yup from 'yup';
import TextArea from "@/components/Form/TextArea";
import TextField from "@/components/Form/TextField";
import { useAppConfig } from '@/contexts/AppConfigContext';
import { useRouter } from 'next/navigation';
import PageTitle from '@/components/pages/PageTitle';
import useCrudService from '@/hooks/useCrudService';
import { HTTP_CREATED, HTTP_OK, HTTP_UNPROCESSABLE_ENTITY } from '@/constants/HttpStatusCode';
import { ITEM_PRIORITY_RULES_API_ENDPOINTS } from '../../SettingsApiEndPoints';
import { ERROR, notify, SUCCESS } from '@/utils/helpers';
import { SETTING_MODULES } from '@/app/warehouse/constants/routes';
import { ACTIVE_INACTIVE_OPTIONS } from '@/constants/static-const';
import SwitchWrapper from '@/components/Form/Switch';
import { fetchData } from "@/app/(smart-office)/(custom-fields)/services/apiService";
import { CUSTOM_FIELD_ENDPOINTS } from "@/app/(smart-office)/(custom-fields)/constants/CustomFieldApiEndpoints";
import CustomFieldInputsAndDrawer from "@/app/(smart-office)/(custom-fields)/_components/CustomFieldInputsAndDrawer";
import { Description } from '@mui/icons-material';
import Loading from "@/app/loading"
import { CUSTOMER_API_ENDPOINTS, ITEM_CATEGORY_API_ENDPOINTS } from '../../SettingsApiEndPoints';
import MultiSelectForCustomField from "@/components/Form/MultiSelectForCustomField";
import CreateNewSelectDropdown from "@/components/Form/CreateNewSelectDropdown";

const ItemPriorityRulesCreate = () => {
  const createUrl = `${ITEM_PRIORITY_RULES_API_ENDPOINTS.ITEM_PRIORITY_RULES_CREATE}`;
  const {
    isLoading,
    create,
  } = useCrudService(createUrl);
  const formikRef = useRef();
  const router = useRouter();
  const [isSubmitting, setSubmitting] = useState(false);
  const { useDictionary } = useAppConfig()
  const dictionary = useDictionary();
  const theme = useTheme();
  const [customerTypeOptions, setCustomerTypeOptions] = useState([]);

  // Define RULE_OPTIONS
  const RULE_OPTIONS = {
    1: "First in First Out (FIFO)",
    2: "Last in First Out (LIFO)",
    3: "First Expired First Out (FEFO)"
  };


  // for custom field need to set initial values starts
  const [initialValues, setInitialValues] = useState({
    company_id: '9f694334-8ece-41a4-8d68-a7fc8d0782d2',
    rule: '',
    item_category_id: '',
    row_status: '1'
  });
  // for custom field need to set initial values ends
  const itemPriorityRulesManagement = dictionary?.settings?.itemPriorityRulesManagement;
  const validationSchema = Yup.object({

    row_status: Yup.string()
      .required(itemPriorityRulesManagement.statusValidation)
      .oneOf(["0", "1"], 'Status must be 0 or 1')
  });
  const handleSubmit = async (values, { setErrors, setSubmitting }) => {
    const response = await create(values, { url: createUrl })
    if ([HTTP_CREATED, HTTP_OK].includes(response.status)) {
      notify(itemPriorityRulesManagement?.addSuccess, SUCCESS);
      setSubmitting(false);
      goToList();
    } else if (response?.status === HTTP_UNPROCESSABLE_ENTITY) {
      setSubmitting(true);
      const errorData = response?.errors;
      setErrors(errorData);
      const errorMessage = response?.message;
      notify(errorMessage, ERROR);
      setSubmitting(false);
    } else {
      const errorMessage = response?.message;
      notify(errorMessage, ERROR);
      setSubmitting(false);
    }
  };

  const {
    data: whLocationListResponse,
    isLoading: isLocationLoading
  } = useCrudService(ITEM_CATEGORY_API_ENDPOINTS.ITEM_CATEGORY_LIST, {});

  const itemCategoryOptions = Array.isArray(whLocationListResponse?.data)
    ? whLocationListResponse.data.map(e => ({
      value: e.id,
      name: e.name
    }))
    : Array.isArray(whLocationListResponse?.data?.data)
      ? whLocationListResponse.data.data.map(e => ({
        value: e.id,
        name: e.name
      }))
      : [];


  const goToList = () => {
    router.push(SETTING_MODULES.ITEM_PRIORITY_RULES_LIST);
  };
  const ruleOptions = Object.keys(RULE_OPTIONS).map((key) => ({
    value: key,
    name: RULE_OPTIONS[key]
  }));

  // custom field implement code starts
  const [isCustomFieldAvailable, setIsCustomFieldAvailable] = useState(false);
  const [customFieldValidate, setCustomFieldValidate] = useState(
    () => () => ({})
  );
  const [customFieldData, setCustomFieldData] = useState(false);
  const { config } = useAppConfig();

  const fetchResponseInputType = useCallback(async () => {
    const formInformation = await fetchData(
      CUSTOM_FIELD_ENDPOINTS.IS_CUSTOM_FIELDS,
      {
        code: "warehouse_setting_ITEM_PRIORITY_RULES",
        table_row_id: "",
      }
    );
    if (formInformation?.data) {
      setIsCustomFieldAvailable(true);
      setCustomFieldData(formInformation?.data);
    }
  }, [config?.isCustomFieldSubmit]);

  useEffect(() => {
    fetchResponseInputType();
  }, [fetchResponseInputType]);
  // custom field implement code ends

  return (
    <>
      {isLoading ? (
        <Loading />
      ) : (
        <Grid container spacing={2} py={2} margin="auto" >
          <Grid item xs={12}>
            <PageTitle title={itemPriorityRulesManagement?.addTitle}
              backToUrl={SETTING_MODULES.ITEM_PRIORITY_RULES_LIST} />
          </Grid>
          <Grid item xs={12}>
            <Formik
              innerRef={formikRef}
              initialValues={initialValues}
              validationSchema={validationSchema}
              enableReinitialize={true}
              onSubmit={handleSubmit}
              // for custom field need to set customFieldValidate starts
              validate={customFieldValidate}
              // for custom field need to set customFieldValidate ends
              validateOnMount={true}
            >
              {({ handleSubmit, values, handleBlur, isValid, isSubmitting, setFieldValue, }) => (
                <Form>
                  <Grid container spacing={2} direction="row" xs={8} md={8} margin="auto">
                    <Grid item lg={12} md={12} xs={12}>
                      {/* Rule Dropdown */}
                      <CreateNewSelectDropdown
                        name="rule"
                        label="Select Rule"
                        onBlur={handleBlur}
                        fullWidth
                        value={values.rule}
                        options={ruleOptions}  // Set the rule options
                        onChange={(e) => setFieldValue('rule', e.target.value)} // Handle change for Formik
                      />
                    </Grid>

                    {<Grid item lg={12} md={12} xs={12}>
                      <MultiSelectForCustomField
                        name="item_category_id"
                        label="Item Category"
                        options={itemCategoryOptions}
                        showCheckboxes={false}
                        isMore={false}
                        limitTags={3}
                      />
                    </Grid>}

                    <Grid item lg={12} md={12} xs={12}>
                      <SwitchWrapper
                        name="row_status"
                        title={itemPriorityRulesManagement.statusTitle}
                        subTitle={itemPriorityRulesManagement.statusSubtitle}
                        label={itemPriorityRulesManagement.statusLabel}
                      />
                    </Grid>

                    {/*custom field implement code starts*/}
                    <CustomFieldInputsAndDrawer
                      key={"bottom"}
                      isCustomFieldAvailable={isCustomFieldAvailable}
                      isCustomFieldInputs={true}
                      isCustomFieldDrawer={true}
                      values={values}
                      isAddExisting={true}
                      customFieldData={customFieldData}
                      position={"bottom"}
                      setInitialValues={setInitialValues}
                      setCustomFieldValidate={setCustomFieldValidate}
                    />
                    {/*custom field implement code ends*/}

                    <Grid pl={1}>
                      <Box sx={{ mb: 0 }}>
                        <div>
                          <Button
                            variant="contained"
                            type="submit"
                            sx={{
                              mt: 2,
                              mr: 2,
                              ml: 1,
                              fontWeight: 800,
                              "&.Mui-disabled": {
                                background: theme.palette.action.disabled,
                                color: theme.palette.text.disabled,
                              },
                            }}
                            size="small"
                            disabled={!isValid || isSubmitting} // Disable button if form is invalid or submitting
                          >
                            {dictionary?.common?.save}
                          </Button>
                        </div>
                      </Box>
                    </Grid>
                  </Grid>
                </Form>
              )}
            </Formik>
          </Grid>
        </Grid>
      )}
    </>
  );
}

export default ItemPriorityRulesCreate;