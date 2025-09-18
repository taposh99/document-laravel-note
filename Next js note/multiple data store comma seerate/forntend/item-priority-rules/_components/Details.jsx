import DialogHeader from "@/components/DialogBox/DialogHeader";
import {
    Avatar,
    Dialog,
    DialogContent,
    Stack,
    styled,
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Typography,
    Grid,
    useTheme,
    Chip
} from "@mui/material";
import React from "react";

const CustomDialog = styled(Dialog)(({ theme }) => ({
    "& .MuiPaper-root": {
        boxShadow:
            "-20px 20px 40px -4px rgba(145, 158, 171, 0.24), 0px 0px 2px 0px rgba(145, 158, 171, 0.24)",
        padding: "24px 24px",
        borderRadius: "8px",
        background: theme.layout.default.background,
    },
    "& .MuiDialogContent-root": {
        padding: 0,
        marginTop: "23px",
    },
    "& .MuiTableCell-root": {
        border: "none",
    },
}));
const Details = ({ open, onClose, data }) => {
    const theme = useTheme();
    const locationStatus = [
    <Chip
        label="Inactive"
        size="small"
        sx={{
            borderRadius: '6px',
            backgroundColor: `${theme.palette.warning.transparent["16%"]}`,
            color: `${theme.palette.warning.dark}`,
            fontWeight: 700,
            fontSize: 12,
        }}
    />,
    <Chip
        label="Active"
        size="small"
        sx={{
            borderRadius: '6px',
            backgroundColor: `${theme.palette.success.transparent["16%"]}`,
            color: `${theme.palette.success.dark}`,
            fontWeight: 700,
            fontSize: 12,
        }}
    />
    ];
    const locStatus = (rowStatus) => {
        return locationStatus[rowStatus] || <></>;
    }
    return (
        <CustomDialog open={open} fullWidth={true} maxWidth="sm">
            <DialogHeader variant="subtitle1" mb={2} mt={3} onClose={onClose}>
                <Typography variant="h6">Details</Typography>
            </DialogHeader>
            <Stack
                p={3}
                mt={1}
                borderRadius={2}
                sx={{
                    background: `linear-gradient(0deg, #FFFFFF, #FFFFFF), linear-gradient(250.95deg, rgba(219, 224, 240, 0.08) 0%, rgba(234, 220, 239, 0.08) 100.42%)`,
                    boxShadow: '0px 1px 2px 0px #919EAB29',
                }}
                gap={4}
                >
                <Grid container spacing={2}>
                    {/* First Row: Name */}
                    <Grid item xs={6}>
                        <Typography variant='body1' sx={{ color: theme.palette.text.secondary, fontSize: 12 }}>
                            Name
                        </Typography>
                        <Typography variant='body1' sx={{ color: theme.palette.text.primary }}>
                            {data?.data?.name}
                        </Typography>
                    </Grid>

                    {/* First Row: Status */}
                    <Grid item xs={6}>
                        <Typography variant='body1' sx={{ color: theme.palette.text.secondary, fontSize: 12 }}>
                            Status
                        </Typography>
                        <Typography variant='body1' sx={{ color: theme.palette.text.primary }}>
                            {locStatus(data?.data?.row_status)}
                        </Typography>
                    </Grid>

                    {/* Second Row: Description */}
                    <Grid item xs={12}>
                        <Typography variant='body1' sx={{ color: theme.palette.text.secondary, fontSize: 12 }}>
                            Description
                        </Typography>
                        <Typography variant='body1' sx={{ color: theme.palette.text.primary }}>
                            {data?.data?.description}
                        </Typography>
                    </Grid>
                </Grid>
            </Stack>

        </CustomDialog>
    );
};

export default Details;
