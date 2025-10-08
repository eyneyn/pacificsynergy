<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Production Report</title>
    <style>
    @page {
        size: A4;
        margin: 10mm 10mm 10mm 10mm; /* narrow margins: top, right, bottom, left */
    }
    body {
        font-family: 'Arial', sans-serif;
        font-size: 11px;
        color: black;
        margin: 0;
        padding: 0;
        border: 1px solid #000;
    }
    </style>
</head>
<body>

<!-- Header Layout -->
<table style="width: 100%; border-collapse: collapse; table-layout: fixed;">

    <tr style="height: 80px;">
        <!-- Logo Section -->
        <td style="width: 10%; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: center; vertical-align: middle; background-color: white;">
            <img src="{{ public_path('img/default.jpg') }}" alt="Default" style="width: 45px;">
        </td>

        <!-- Center Title Section -->
        <td style="width: 65%; border-bottom: 1px solid #000; border-right: 1px solid #000; background-color: #2d326b; color: white; text-align: center; vertical-align: middle; font-weight: bold; font-size: 18px;">
            DAILY PRODUCTION REPORT
        </td>

        <!-- Document Info -->
        <td style="width: 25%; border-bottom: 1px solid #000; padding: 0;">
            <table style="width: 100%; border-collapse: collapse; font-size: 7px; text-align: center;">
                <tr>
                    <td style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 2px 5px;">Document Number:</td>
                    <td style="border-bottom: 1px solid #000; padding: 2px 5px;">PR-FR-053</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 2px 5px;">Effective Date:</td>
                    <td style="border-bottom: 1px solid #000; padding: 2px 5px;">1-Mar-25</td>
                </tr>
                <tr>
                    <td style="border-bottom: 1px solid #000; border-right: 1px solid #000; padding: 2px 5px;">Revision Number:</td>
                    <td style="border-bottom: 1px solid #000; padding: 2px 5px;">03</td>
                </tr>
                <tr>
                    <td style="border-right: 1px solid #000; padding: 2px 5px;">Page No.:</td>
                    <td style="padding: 2px 5px;">1 of 1</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Line Info and AC Temperature Section -->
<table style="width: 100%; font-size: 6px; margin-bottom: 15px; table-layout: fixed;">
    <tr>
        <!-- Left Side: Line Info -->
        <td style="width: 30%; vertical-align: top; padding-right: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">LINE #:</td>
                    <td style="text-align: center; border-bottom: 0.5px solid #000; padding-left: 5px;">{{ $report->line ?? '' }}</td>
                </tr>
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">Production Date:</td>
                    <td style="text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ \Carbon\Carbon::parse($report->production_date)->format('l, F d, Y') }}</td>
                </tr>
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">Shift:</td>
                    <td style="text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ $report->shift ?? '' }}</td>
                </tr>
            </table>
        </td>

        <td style="width: 40%">
        </td>

        <!-- Right Side: AC Temperature -->
        <td style="width: 20%; vertical-align: top; padding-left: 10px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">AC1 Temperature:</td>
                    <td style="width: 20%; text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ $report->ac1 ?? '' }}°C</td>
                </tr>
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">AC2 Temperature:</td>
                    <td style="width: 20%; text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ $report->ac2 ?? '' }}°C</td>
                </tr>
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">AC3 Temperature:</td>
                    <td style="width: 20%; text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ $report->ac3 ?? '' }}°C</td>
                </tr>
                <tr>
                    <td style="width: 35%; text-align: right; font-weight: bold; padding-right: 10px;">AC4 Temperature:</td>
                    <td style="width: 20%; text-align: center; border-bottom: 0.5px solid #000;">&nbsp;{{ $report->ac4 ?? '' }}°C</td>
                </tr>
            </table>
        </td>

        <td style="width: 10%">
        </td>
    </tr>
</table>

<!-- SKU Output Table -->
<table style="width: 100%; table-layout: fixed; border-collapse: collapse; font-size: 7px;">
    <thead>
        <tr style="background-color: #2d326b; color: white; font-weight: bold; text-align: center;">
            <th style="width: 20%; padding: 4px; border-top: 1px solid #000; border-right: 1px solid #000; border-bottom: 1px solid #000;">SKU</th>
            <th style="width: 20%; border: 1px solid #000;">FBO./FCO(H)</th>
            <th style="width: 20%; border: 1px solid #000;">LBO./LCO(H)</th>
            <th style="width: 20%; border: 1px solid #000;">SKU TOTAL OUTPUT</th>
        </tr>
    </thead>
    <tbody>
        <tr style="height: 22px;">
            <td style="width: 20%; padding: 7px; border-right: 1px solid #000; text-align: center;">
                {{ $report->standard->description ?? '' }}
            </td>
            <td style="width: 20%; border-right: 1px solid #000; text-align: center;">
                {{ $report->fbo_fco ?? '' }}
            </td>
            <td style="width: 20%; border-right: 1px solid #000; text-align: center;">
                {{ $report->lbo_lco ?? '' }}
            </td>
            <td style="width: 20%; border-right: 1px solid #000; background-color: #fef4cc; text-align: center; padding-right: 5px;">
                {{ $report->total_outputCase ?? '' }} cases
            </td>
        </tr>
    </tbody>
</table>

<!-- Filling Line Section -->
<table style="width: 100%; background-color: #d4edff; border-collapse: collapse; font-size: 7px; margin-bottom: 10px;">
    <!-- Header Row -->
    <tr>
        <td colspan="2" style="background-color: #2d326b; color: white; font-weight: bold; text-align: center; border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 4px;">
            FILLING LINE
        </td>
    </tr>

    <!-- Content Row -->
    <tr style="vertical-align: top; border-bottom: 1px solid #000;">
        <!-- Left Side: Filler Speed and RM Rejects -->
        <td style="width: 60%; padding: 15px 70px;">
            <table style="width: 100%; font-size: 7px; border-collapse: collapse;">
                <!-- Filler Speed Row -->
                <tr>
                    <td colspan="3" style="font-weight: bold; padding-bottom: 4px;">Speed (Bottle per Hour)</td>
                </tr>
                <tr style="height: 22px;">
                    <td style="width: 25%;">Filler Speed:</td>
                    <td style="border-bottom: 0.5px solid #000; width: 50%; text-align: center;">
                        {{ $report->filler_speed ?? '' }}
                    </td>
                    <td style="width: 25%; padding-left: 4px;">bph</td>
                </tr>

                <!-- RM Rejects Label -->
                <tr>
                    <td colspan="3" style="font-weight: bold; padding-top: 10px; padding-bottom: 4px;">RM Rejects (Quantity / Code)</td>
                </tr>

                <!-- OPP Labels -->
                <tr style="height: 22px;">
                    <td style="width: 25%;">OPP Labels (in pcs):</td>
                    <td style="border-bottom: 0.5px solid #000; width: 50%; text-align: center;">
                        {{ $report->opp_labels ?? '' }}
                    </td>
                    <td style="width: 25%; padding-left: 4px;">pcs</td>
                </tr>

                <!-- Shrinkfilm -->
                <tr style="height: 22px;">
                    <td style="width: 25%;">Shrinkfilm (in pcs):</td>
                    <td style="border-bottom: 0.5px solid #000; width: 50%; text-align: center;">
                        {{ $report->shrinkfilm ?? '' }}
                    </td>
                    <td style="width: 25%; padding-left: 4px;">pcs</td>
                </tr>
            </table>
        </td>

        <!-- Right Side: Bottle Code, Labeler Speed, Caps, Bottles -->
        <td style="width: 40%; padding: 12px 30px;">
            <table style="width: 100%; font-size: 7px; border-collapse: collapse;">
                <!-- Bottle Code -->
                <tr style="height: 22px;">
                    <td style="width: 50%; text-align: right; font-weight: bold; padding-right: 10px;">Bottle Code:</td>
                    <td style="border-bottom: 0.5px solid #000; text-align: center; font-weight: bold;">
                        {{ $report->bottle_code ?? '' }}
                    </td>
                </tr>

                <!-- Spacer between Bottle Code and Labeler Speed -->
                <tr><td colspan="2" style="height: 8px;"></td></tr>

                <!-- Labeler Speed -->
                <tr style="height: 22px;">
                    <td style="text-align: right; font-weight: bold; padding-right: 10px;">OPP / Labeler Speed:</td>
                    <td style="border-bottom: 0.5px solid #000; text-align: center;">
                        {{ $report->opp_labeler_speed ?? '' }}
                    </td>
                </tr>

                <!-- Optional empty label row to align with RM Rejects on the left -->
                <tr>
                    <td colspan="2" style="padding-top: 10px; padding-bottom: 4px; font-weight: bold;">&nbsp;</td>
                </tr>

                <!-- Caps -->
                <tr style="height: 22px;">
                    <td style="text-align: right; padding-right: 10px;">Caps (in pcs):</td>
                    <td style="border-bottom: 0.5px solid #000; text-align: center;">
                        {{ $report->caps_filling ?? '' }}
                    </td>
                </tr>

                <!-- Bottles -->
                <tr style="height: 22px;">
                    <td style="text-align: right; padding-right: 10px;">Bottle (in pcs):</td>
                    <td style="border-bottom: 0.5px solid #000; text-align: center;">
                        {{ $report->bottle_filling ?? '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Total Downtime Section (Right Aligned) -->
<table style="width: 100%; font-size: 7px; border-collapse: collapse; margin-bottom: 10px;">
    <tr>
        <td style="width: 60%;"></td> <!-- Empty left side -->
        <td style="width: 40%; padding-right: 30px; text-align: right;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="height: 24px;">
                    <td style="font-weight: bold; text-align: right; width: 50%; padding-right: 4px;">TOTAL DOWNTIME :</td>
                    <td style="border-bottom: 0.5px solid #000; width: 20%; text-align: center;">
                        {{ $report->total_downtime ?? '' }}
                    </td>
                    <td style="width: 10%; padding-left: 2px;">Mins</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Issues / Downtime / Remarks Table -->
<table style="width: 100%; border-bottom: 0; border-collapse: collapse; font-size: 7px;">
    <thead>
        <tr style="background-color: #2d326b; color: white; font-weight: bold; text-align: center;">
            <th style="border: 1px solid #000; width: 20%; padding: 4px;">MACHINE / OTHERS</th>
            <th style="border: 1px solid #000; width: 15%;">TYPES</th>
            <th style="border: 1px solid #000; width: 45%;">ISSUE'S / DOWN TIME / REMARKS</th>
            <th style="border: 1px solid #000; width: 20%;">NO. OF MINS</th>
        </tr>
    </thead>
    <tbody>
        @for($i = 0; $i < 13; $i++)
        <tr style="height: 22px;">
            <td style="border: 1px solid #000; text-align: center; padding: 8px;">
                {{ $report->issues[$i]->maintenance->name ?? '' }}
            </td>
            <td style="border: 1px solid #000; text-align: center; padding: 8px;">
                {{ $report->issues[$i]->maintenance->type ?? '' }}
            </td>
            <td style="border: 1px solid #000; text-align: center; padding: 8px;">
                {{ $report->issues[$i]->remarks ?? '' }}
            </td>
            <td style="border: 1px solid #000; text-align: center; padding: 8px;">
                {{ $report->issues[$i]->minutes ?? '' }}
            </td>
        </tr>
        @endfor
    </tbody>
</table>

<!-- Blow Molding Section -->
<table style="width: 100%; background-color: #d4edff; border-collapse: collapse; font-size: 7px;">
    <!-- Header Row -->
    <tr>
        <td colspan="3" style="background-color: #2d326b; color: white; font-weight: bold; text-align: center; padding: 4px;">
            BLOW MOLDING
        </td>
    </tr>

    <!-- Content Row -->
    <tr style="width: 100%; background-color: ;">
        <td style="padding: 15px 70px;">
            <table style="width: 100%; font-size: 7px; border-collapse: collapse;">
                <!-- Blow Molding Output -->
                <tr style="height: 22px;">
                    <td style="width: 25%; font-weight: bold; text-align: right; padding-right: 10px;">Blow Molding Output:</td>
                    <td style="border-bottom: 0.5px solid #000; width: 20%; text-align: center;">
                        {{ $report->blow_molding_output ?? '' }}
                    </td>
                    <td style="width: 10%; padding-left: 4px;">pcs</td>
                </tr>

                <!-- Speed (Bottles / Hours) -->
                <tr style="height: 22px;">
                    <td style="width: 25%; font-weight: bold; text-align: right; padding-right: 10px;">Speed (Bottles / Hours):</td>
                    <td style="border-bottom: 0.5px solid #000; width: 20%; text-align: center;">
                        {{ $report->speed_blow_molding ?? '' }}
                    </td>
                    <td style="width: 10%; padding-left: 4px;">pcs</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<!-- Blow Molding  Rejects -->
<table style="width: 100%; border-collapse: collapse; font-size: 7px;">
    <!-- Header Row -->
    <tr>
        <td colspan="4" style="background-color: #2d326b; color: white; font-weight: bold; text-align: center; padding: 4px;">
            BLOW MOLDING REJECTS
        </td>
    </tr>
    <tr>
        <td style="width: 20%; font-weight: bold; padding: 5px 0; background-color: #ccc9c9; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: right;">
            PREFORM (in pcs):
        </td>
        <td style="width: 20%; border: 1px solid #000; text-align: center;">
            {{ $report->preform_blow_molding ?? '' }}
        </td>
        <td style="width: 20%; font-weight: bold; background-color: #ccc9c9; border: 1px solid #000; text-align: right;">
            BOTTLES (in pcs):
        </td>
        <td style="width: 20%; border-top: 1px solid #000; border-bottom: 1px solid #000; text-align: center; padding-right: 5px;">
            {{ $report->bottles_blow_molding ?? '' }}
        </td>
    </tr>
</table>

<!-- QA Remarks and Line QC Rejects Section -->
<table style="width: 100%; border-collapse: collapse; font-size: 7px;">
    <tr>
        <td colspan="4" style="padding: 4px; background-color: #2d326b; border-right: 1px solid #000; color: white; text-align: center; font-weight: bold;">QA REMARKS</td>
        <td colspan="8" style="padding: 4px; background-color: #2d326b; color: white; text-align: center; font-weight: bold;">LINE QC REJECTS</td>
    </tr>
    <tr>
        <td colspan="4" rowspan="20" style="vertical-align: top; padding: 0; border-right: 1px solid #000;">
            <div style="text-align: center; padding: 4px 2px;">
                <strong>Ozone :</strong>
                <span style="color: #0b5394; font-weight: bold;">{{ strtoupper($report->qa_remarks ?? 'PASSED') }}</span>
            </div>

            <!-- QA SAMPLE Title Bar -->
            <div style="padding: 4px; background-color: #2d326b; color: white; text-align: center; font-weight: bold; font-size: 7px; padding: 2px 0;">
                QA SAMPLE
            </div>

            <!-- QA SAMPLE Table -->
            <table style="width: 100%; border-top: 1px solid #000; border-collapse: collapse; font-size: 7px;">
                <tr style="background-color: #2d326b; border-bottom: 1px solid #000; color: white;">
                    <th style="padding: 2px; text-align: right; border-right: 1px solid #000;">SKU's :</th>
                    <td style="padding: 2px; background-color: #fef4cc; color: #000; text-align: center;">{{ $report->standard->description ?? '' }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: right; ">Total (in pcs):</td>
                    <td style="padding: 2px; border-bottom: 1px solid #000; text-align: center;">{{ $report->total_sample ?? '' }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: right; ">w/ Label (in pcs):</td>
                    <td style="padding: 2px; border-bottom: 1px solid #000; text-align: center;">{{ $report->with_label ?? '' }}</td>
                </tr>
                <tr>
                    <td style="padding: 2px; border-bottom: 1px solid #000; border-right: 1px solid #000; text-align: right; ">w/o Label (in pcs):</td>
                    <td style="padding: 2px; border-bottom: 1px solid #000; text-align: center;">{{ $report->without_label ?? '' }}</td>
                </tr>
                <tr>
                    <td rowspan="6" style="padding: 5px; border-bottom: 1px solid #000; background-color: #2d326b;"></td>
                    <td rowspan="6" style="border-bottom: 1px solid #000; background-color: #2d326b; "></td>
                </tr>
            </table>
        </td>

        <!-- Line QC Rejects Table -->
        <td style="border-right: 1px solid #000; text-align: center;">SKU:</td>
        <td  colspan="7" style="text-align: center; background-color: #fef4cc; color: #000;">{{ $report->standard->description ?? '' }}</td>

        <tr>
            <td colspan="2" style="font-size: 6px; background-color: #2d326b; color: white; border-right: 1px solid #000; border-top: 1px solid #000; text-align: center;">REJECTS</td>
            <td colspan="1" style="font-size: 6px; background-color: #2d326b; color: white; border-right: 1px solid #000; border-top: 1px solid #000; text-align: center;">TOTAL</td>
            <td colspan="1" style="font-size: 6px; border-right: 1px solid #000; border-top: 1px solid #000; text-align: center;"></td>
            <td colspan="2" style="font-size: 6px; background-color: #2d326b; color: white; border-right: 1px solid #000; border-top: 1px solid #000; text-align: center;">REJECTS</td>
            <td colspan="1" style="font-size: 6px; background-color: #2d326b; color: white; border-right: 1px solid #000; border-top: 1px solid #000; text-align: center;">TOTAL</td>
            <td colspan="1" style="font-size: 6px; border-top: 1px solid #000; text-align: center;"></td>
        </tr>
    </tr>

        @for ($i = 0; $i < 10; $i++)
        <tr>
            <!-- Line QC Table 1 -->
            <td style="text-align: center; border: 1px solid #000;" colspan="2">{{ $report->lineQcRejects[$i]->defect->defect_name ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $report->lineQcRejects[$i]->quantity ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">pcs</td>

            <!-- Line QC Table 2 -->
            <td style="text-align: center; border: 1px solid #000;" colspan="2">{{ $report->lineQcRejects[$i+6]->defect->defect_name ?? '' }}</td>
            <td style="text-align: center; border: 1px solid #000;">{{ $report->lineQcRejects[$i+6]->quantity ?? '' }}</td>
            <td style="text-align: center; border-top: 1px solid #000; border-left: 1px solid #000; border-bottom: 1px solid #000;">pcs</td>
        </tr>
        @endfor
        <tr>
            <td colspan="4" style="padding: 5px 5px 6px 5px; border-bottom: 1px solid #000; background-color: #2d326b;"></td>
            <td  colspan="8" style="border-bottom: 1px solid #000; background-color: #2d326b; "></td>
        </tr>
</table>

<!-- Signatories Section (Centered) -->
<div style="width: 80%; margin: 0 auto;">
    <table style="width: 100%; border-collapse: collapse; font-size: 9px;">
        <tr>
            <!-- Prepared by -->
            <td style="width: 40%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="font-weight: bold; padding-bottom: 2px; padding-bottom: 20px;">Prepared by:</td>
                    </tr>
                    @if ($report->user)
                        <tr>
                            <td style="border-bottom: 0.5px solid #000; padding: 4px 0; text-align: center">
                                {{ $report->user->first_name }} {{ $report->user->last_name }} - {{ $report->user->getRoleNames()->first() ?? '' }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td style="text-align: center">Printed Name and Signature</td>
                    </tr>
                </table>
            </td>

            <td style="width: 10%">

            </td>

            <!-- Noted by -->
            <td style="width: 40%;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: right; font-weight: bold; padding-bottom: 20px;">Noted by:</td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 0.5px solid #000; padding: 4px 0; text-align: center; color: white;">
                            .
                        </td>
                    </tr>
                    <tr>
                        
                    </tr>
                    <tr>
                        <td style="text-align: center">Printed Name and Signature</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>
