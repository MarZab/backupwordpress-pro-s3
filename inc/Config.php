<?php

$container['addon_class'] = 'HM\\BackUpWordPress\\Addon';
$container['checklicense_class'] = 'HM\\BackUpWordPress\\CheckLicense';
$container['addon_version'] = '2.0.4';
$container['min_bwp_version'] = '3.1.4';
$container['edd_download_file_name'] = 'BackUpWordPress To Amazon S3';
$container['addon_settings'] = 'hmbkpp_aws_settings';
$container['transient_name'] = 'hmbkp_license_data_s3';
$container['service_class'] = 'S3BackUpService';
$container['updater_class'] = 'HM\\BackUpWordPress\\PluginUpdater';
