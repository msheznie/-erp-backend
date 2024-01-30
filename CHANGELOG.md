# Changelog

## [10.30.2](https://github.com/pbsgears/Gears_BackEnd/compare/v10.30.1...v10.30.2) (2024-01-18)


### Bug Fixes

* **accounts payable:** supplier ledger invalid data load issue fixed [GCP-2727] ([#5477](https://github.com/pbsgears/Gears_BackEnd/issues/5477)) ([4df8205](https://github.com/pbsgears/Gears_BackEnd/commit/4df8205e631089a792f85393201eba57a65b08ff))
* **general ledger:** budget upload template download issue fixed [GCP-2731] ([#5479](https://github.com/pbsgears/Gears_BackEnd/issues/5479)) ([0775ea3](https://github.com/pbsgears/Gears_BackEnd/commit/0775ea3da3b12a021684990925b01b383043cffd))
* **inventory:** direct materialissue item cannot be added and qnty cannot be change fixed [GCP-2730] ([#5478](https://github.com/pbsgears/Gears_BackEnd/issues/5478)) ([ab4980b](https://github.com/pbsgears/Gears_BackEnd/commit/ab4980b90902ef9ee37dc96f803e62c84c37332c))

## [10.30.0](https://github.com/pbsgears/Gears_BackEnd/compare/v10.29.1...v10.30.0) (2024-01-16)


### Features

* **general ledger:** format credit and debit values with thousand seperators [GCP-2653] ([#5397](https://github.com/pbsgears/Gears_BackEnd/issues/5397)) ([851abca](https://github.com/pbsgears/Gears_BackEnd/commit/851abca12946e03d5ecdfa694cc7b763ffc0a349))
* **pos:** added default conditions for customer master pull api [GCP-2681] ([#5433](https://github.com/pbsgears/Gears_BackEnd/issues/5433)) ([6260b5a](https://github.com/pbsgears/Gears_BackEnd/commit/6260b5a7d253955d2670a4a983a4b25095f763b0))
* **pos:** added posCustomerID to pull_customer_master api [GCP-2628] ([#5409](https://github.com/pbsgears/Gears_BackEnd/issues/5409)) ([7e0a45b](https://github.com/pbsgears/Gears_BackEnd/commit/7e0a45be7db26a95e197c904e6ec09f428f964cf))
* **system admin:** fluent bit and loki implementation for audit logs [GCP-2531] ([#5398](https://github.com/pbsgears/Gears_BackEnd/issues/5398)) ([662471a](https://github.com/pbsgears/Gears_BackEnd/commit/662471a523f487eb7684769c6de98a0762ae0920))
* **system admin:** supplier Approval Email Enhancement [GCP-2691] ([#5438](https://github.com/pbsgears/Gears_BackEnd/issues/5438)) ([9614efc](https://github.com/pbsgears/Gears_BackEnd/commit/9614efc40a16681b54174857b92de71bdb116d5d))
* **system admin:** supplier Reopen & rejection Email Enhancement [GCP-2704] ([#5439](https://github.com/pbsgears/Gears_BackEnd/issues/5439)) ([9cb570e](https://github.com/pbsgears/Gears_BackEnd/commit/9cb570ead9ba8b3d3343201ebca1da4d1221ff7e))


### Bug Fixes

* **accounts payable:** Fixed sentry issue in supplier Invoice [GCP-2669] ([#5419](https://github.com/pbsgears/Gears_BackEnd/issues/5419)) ([2b16579](https://github.com/pbsgears/Gears_BackEnd/commit/2b16579b3c872cc9571f7ba5e3039613d8863a9b))
* **accounts receivable:** isset condition checked for the chartaccount query [GCP-2555] ([#5393](https://github.com/pbsgears/Gears_BackEnd/issues/5393)) ([7d4af64](https://github.com/pbsgears/Gears_BackEnd/commit/7d4af6473a555ce1069367f11591ce5fe608ab75))
* **approval setup:** some time level id missing in approval metrix filter,now validated[GCP-2670] ([#5423](https://github.com/pbsgears/Gears_BackEnd/issues/5423)) ([d1d6c46](https://github.com/pbsgears/Gears_BackEnd/commit/d1d6c4657ae8c14e030f3ca8a21d73142a959ec1))
* **asset management:** asset Register Report The report is showing the disposed assets [GCP-2692] ([#5435](https://github.com/pbsgears/Gears_BackEnd/issues/5435)) ([7272722](https://github.com/pbsgears/Gears_BackEnd/commit/727272216ea7c036126f0e9fe7e00ac085148c07))
* **common:** employee invoice document approval issue [GCP-2721] ([#5441](https://github.com/pbsgears/Gears_BackEnd/issues/5441)) ([6af636a](https://github.com/pbsgears/Gears_BackEnd/commit/6af636a9376d0c19c386e8ccf9b2967c6f48bd55))
* **common:** excel export throws max_memory_limit issue fixed [GCP-2665] ([#5412](https://github.com/pbsgears/Gears_BackEnd/issues/5412)) ([7b84e4d](https://github.com/pbsgears/Gears_BackEnd/commit/7b84e4d125b92d94c27e110cbe6a4c61dfa2acb2))
* **general ledger:** company id null in generallegger report,checked id is null or not  [GCP-2671] ([#5421](https://github.com/pbsgears/Gears_BackEnd/issues/5421)) ([83092da](https://github.com/pbsgears/Gears_BackEnd/commit/83092daae7e8edbda442d6cd7c94b7113b0f92dd))
* **general ledger:** Fixed sentry issue in Salary JV [GCP-2668] ([#5417](https://github.com/pbsgears/Gears_BackEnd/issues/5417)) ([cb5e5a3](https://github.com/pbsgears/Gears_BackEnd/commit/cb5e5a34388dda099ac33a0c7fc7c737ce15e346))
* **inventory:** material issue partially and fully issued qnty validation [GCP-2435] ([#5404](https://github.com/pbsgears/Gears_BackEnd/issues/5404)) ([fa012eb](https://github.com/pbsgears/Gears_BackEnd/commit/fa012ebd40a6b84aadcf1febc547a3b6ecbb837a))
* **navigation:** in user group assign store function isset condition checked [GCP-2667] ([#5414](https://github.com/pbsgears/Gears_BackEnd/issues/5414)) ([0b8953c](https://github.com/pbsgears/Gears_BackEnd/commit/0b8953ce571d445d2512633889ce705e54c7d936))


### Performance Improvements

* **general ledger:** navigation routes of budget review [GCP-2601] ([#5399](https://github.com/pbsgears/Gears_BackEnd/issues/5399)) ([4f16d6f](https://github.com/pbsgears/Gears_BackEnd/commit/4f16d6f7eba72ffe1cadd6e291060efbcb7fca6c))

## [10.29.1](https://github.com/pbsgears/Gears_BackEnd/compare/v10.29.0...v10.29.1) (2024-01-03)


### Bug Fixes

* **asset management:** asset Depreciation Job processing issue fixed [GCP-2622] ([b4b7df7](https://github.com/pbsgears/Gears_BackEnd/commit/b4b7df71babbe0cb375c7ac71a0c9388b4b1ffee))

## [10.29.0](https://github.com/pbsgears/Gears_BackEnd/compare/v10.28.0...v10.29.0) (2024-01-01)


### Features

* **pos:** changed to retrieve gl entries from cogs gl code [GCP-2539] ([1422e96](https://github.com/pbsgears/Gears_BackEnd/commit/1422e96fe1c2a1c61ee10bff28b7cb2c2f4fa7d8))
* **pos:** company filter applied for shift details [GCP-2591] ([#5357](https://github.com/pbsgears/Gears_BackEnd/issues/5357)) ([42d3061](https://github.com/pbsgears/Gears_BackEnd/commit/42d306142af06cd71833a161497cc8150479d689))
* **POS:** customer invoice vat, discount and gl entry developments [GCP-2504] ([#5338](https://github.com/pbsgears/Gears_BackEnd/issues/5338)) ([8c9ad5c](https://github.com/pbsgears/Gears_BackEnd/commit/8c9ad5c259d36aa932e7546db8d984a073ca4d53))
* **pos:** return to amend restricted by isPOS 1 [GCP-2600] ([#5364](https://github.com/pbsgears/Gears_BackEnd/issues/5364)) ([aa85c3a](https://github.com/pbsgears/Gears_BackEnd/commit/aa85c3a7e97748c0ae1c54ddefe2f525b52bdf0b))


### Bug Fixes

* **POS:** general ledger entries developed to post group wise [GCP-2580] ([#5345](https://github.com/pbsgears/Gears_BackEnd/issues/5345)) ([ec9073e](https://github.com/pbsgears/Gears_BackEnd/commit/ec9073e2d6602390c77825435303034bf319f2f5))
* **pos:** minor mis-matches by rounding to 3 decimals [GCP-2597] ([#5361](https://github.com/pbsgears/Gears_BackEnd/issues/5361)) ([d2066ec](https://github.com/pbsgears/Gears_BackEnd/commit/d2066ec5b1bdf8395a5b7dc38f574db2c6fe1bf5))
* **pos:** tax master mapping table joined [GCP-2592] ([#5356](https://github.com/pbsgears/Gears_BackEnd/issues/5356)) ([0beb2b6](https://github.com/pbsgears/Gears_BackEnd/commit/0beb2b625e45a35b97f62e5be3d60141c66726ce))
* **POS:** wac value updating issue fixed [GCP-2602] ([#5367](https://github.com/pbsgears/Gears_BackEnd/issues/5367)) ([bfd7a73](https://github.com/pbsgears/Gears_BackEnd/commit/bfd7a737ffb64495a78890d039a38cd8bb714363))
