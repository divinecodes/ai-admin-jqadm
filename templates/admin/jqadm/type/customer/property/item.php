<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2022
 */

$selected = function( $key, $code ) {
	return ( $key == $code ? 'selected="selected"' : '' );
};

$enc = $this->encoder();
$params = $this->get( 'pageParams', [] );


?>
<?php $this->block()->start( 'jqadm_content' ) ?>

<form class="item item-customer-property-type form-horizontal container-fluid" method="POST" enctype="multipart/form-data" action="<?= $enc->attr( $this->link( 'admin/jqadm/url/save', $params ) ) ?>">
	<input id="item-id" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'item', 'customer.property.type.id' ) ) ) ?>" value="<?= $enc->attr( $this->get( 'itemData/customer.property.type.id' ) ) ?>" />
	<input id="item-next" type="hidden" name="<?= $enc->attr( $this->formparam( array( 'next' ) ) ) ?>" value="get" />
	<?= $this->csrf()->formfield() ?>

	<nav class="main-navbar">
		<h1 class="navbar-brand">
			<span class="navbar-title"><?= $enc->html( $this->translate( 'admin', 'Customer Property Type' ) ) ?></span>
			<span class="navbar-id"><?= $enc->html( $this->get( 'itemData/customer.property.type.id' ) ) ?></span>
			<span class="navbar-label"><?= $enc->html( $this->get( 'itemData/customer.property.type.label' ) ?: $this->translate( 'admin', 'New' ) ) ?></span>
			<span class="navbar-site"><?= $enc->html( $this->site()->match( $this->get( 'itemData/customer.property.type.siteid' ) ) ) ?></span>
		</h1>
		<div class="item-actions">
			<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'itemactions' ), ['params' => $params] ) ?>
		</div>
	</nav>

	<div class="row item-container">

		<div class="col-xl-3 item-navbar">
			<div class="navbar-content">
				<ul class="nav nav-tabs flex-xl-column flex-wrap d-flex box" role="tablist">

					<li class="nav-item basic">
						<a class="nav-link active" href="#basic" data-bs-toggle="tab" role="tab" aria-expanded="true" aria-controls="basic">
							<?= $enc->html( $this->translate( 'admin', 'Basic' ) ) ?>
						</a>
					</li>

					<?php foreach( array_values( $this->get( 'itemSubparts', [] ) ) as $idx => $subpart ) : ?>
						<li class="nav-item <?= $enc->attr( $subpart ) ?>">
							<a class="nav-link" href="#<?= $enc->attr( $subpart ) ?>" data-bs-toggle="tab" role="tab" tabindex="<?= ++$idx + 1 ?>">
								<?= $enc->html( $this->translate( 'admin', $subpart ) ) ?>
							</a>
						</li>
					<?php endforeach ?>

				</ul>

				<div class="item-meta text-muted">
					<small>
						<?= $enc->html( $this->translate( 'admin', 'Modified' ) ) ?>:
						<span class="meta-value"><?= $enc->html( $this->get( 'itemData/customer.property.type.mtime' ) ) ?></span>
					</small>
					<small>
						<?= $enc->html( $this->translate( 'admin', 'Created' ) ) ?>:
						<span class="meta-value"><?= $enc->html( $this->get( 'itemData/customer.property.type.ctime' ) ) ?></span>
					</small>
					<small>
						<?= $enc->html( $this->translate( 'admin', 'Editor' ) ) ?>:
						<span class="meta-value"><?= $enc->html( $this->get( 'itemData/customer.property.type.editor' ) ) ?></span>
					</small>
				</div>

				<div class="more"></div>
			</div>
		</div>

		<div class="col-xl-9 item-content tab-content">

			<div id="basic" class="item-basic tab-pane fade show active" role="tabpanel" aria-labelledby="basic">

				<div class="box">
					<div class="row">
						<div class="col-xl-6 block <?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?>">
							<div class="form-group row mandatory">
								<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Domain' ) ) ?></label>
								<div class="col-sm-8">
									<select class="form-select item-domain" required="required" tabindex="1"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'customer.property.type.domain' ) ) ) ?>"
										<?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?> >
										<option value="">
											<?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?>
										</option>

										<?php foreach( ['customer'] as $domain ) : ?>
											<option value="<?= $enc->attr( $domain ) ?>" <?= $selected( $this->get( 'itemData/customer.property.type.domain', 'customer' ), $domain ) ?> >
												<?= $enc->html( $this->translate( 'admin', $domain ) ) ?>
											</option>
										<?php endforeach ?>
									</select>
								</div>
							</div>
							<div class="form-group row mandatory">
								<label class="col-sm-4 form-control-label"><?= $enc->html( $this->translate( 'admin', 'Status' ) ) ?></label>
								<div class="col-sm-8">
									<select class="form-select item-status" required="required" tabindex="1"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'customer.property.type.status' ) ) ) ?>"
										<?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?> >
										<option value="">
											<?= $enc->html( $this->translate( 'admin', 'Please select' ) ) ?>
										</option>
										<option value="1" <?= $selected( $this->get( 'itemData/customer.property.type.status', 1 ), 1 ) ?> >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:1' ) ) ?>
										</option>
										<option value="0" <?= $selected( $this->get( 'itemData/customer.property.type.status', 1 ), 0 ) ?> >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:0' ) ) ?>
										</option>
										<option value="-1" <?= $selected( $this->get( 'itemData/customer.property.type.status', 1 ), -1 ) ?> >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:-1' ) ) ?>
										</option>
										<option value="-2" <?= $selected( $this->get( 'itemData/customer.property.type.status', 1 ), -2 ) ?> >
											<?= $enc->html( $this->translate( 'mshop/code', 'status:-2' ) ) ?>
										</option>
									</select>
								</div>
							</div>
							<div class="form-group row mandatory">
								<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Code' ) ) ?></label>
								<div class="col-sm-8">
									<input class="form-control item-code" type="text" required="required" tabindex="1"
										name="<?= $enc->attr( $this->formparam( array( 'item', 'customer.property.type.code' ) ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'Unique type code (required)' ) ) ?>"
										value="<?= $enc->attr( $this->get( 'itemData/customer.property.type.code' ) ) ?>"
										<?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?> />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Unique type code' ) ) ?>
								</div>
							</div>
							<div class="form-group row mandatory">
								<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Label' ) ) ?></label>
								<div class="col-sm-8">
									<input class="form-control item-label" type="text" required="required" tabindex="1"
										name="<?= $this->formparam( array( 'item', 'customer.property.type.label' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'Internal name (required)' ) ) ?>"
										value="<?= $enc->attr( $this->get( 'itemData/customer.property.type.label' ) ) ?>"
										<?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?> />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Internal type name shown in the administration interface' ) ) ?>
								</div>
							</div>
							<div class="form-group row optional">
								<label class="col-sm-4 form-control-label help"><?= $enc->html( $this->translate( 'admin', 'Position' ) ) ?></label>
								<div class="col-sm-8">
									<input class="form-control item-position" type="number" step="1" tabindex="1"
										name="<?= $this->formparam( array( 'item', 'customer.property.type.position' ) ) ?>"
										value="<?= $enc->attr( $this->get( 'itemData/customer.property.type.position' ) ) ?>"
										placeholder="<?= $enc->attr( $this->translate( 'admin', 'Type position (optional)' ) ) ?>"
										<?= $this->site()->readonly( $this->get( 'itemData/customer.property.type.siteid' ) ) ?> />
								</div>
								<div class="col-sm-12 form-text text-muted help-text">
									<?= $enc->html( $this->translate( 'admin', 'Order of the types in the frontend' ) ) ?>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>

			<?= $this->get( 'itemBody' ) ?>

		</div>

		<div class="item-actions">
			<?= $this->partial( $this->config( 'admin/jqadm/partial/itemactions', 'itemactions' ), ['params' => $params] ) ?>
		</div>
	</div>
</form>

<?php $this->block()->stop() ?>


<?= $this->render( $this->config( 'admin/jqadm/template/page', 'page' ) ) ?>
