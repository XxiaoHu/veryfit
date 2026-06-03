import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, Button, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import './editor.scss';

const DEFAULT_IMAGES = [
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20multiple%20watch%20bands%20rose%20gold%20black%20silver%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=leopard%20print%20elastic%20watch%20band%20fashion%20accessory%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=blue%20marble%20pattern%20watch%20band%20stylish%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20metallic%20watch%20bands%20black%20gold%20silver%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=rose%20gold%20metal%20watch%20band%20elegant%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=smartwatch%20with%20pearl%20beaded%20band%20luxury%20elegant%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=square%20smartwatch%20with%20digital%20display%20black%20band%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=collection%20of%20silicone%20watch%20bands%20various%20colors%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=row%20of%20colorful%20watch%20bands%20pastel%20colors%20minimal%20background&image_size=square_hd',
	'https://neeko-copilot.bytedance.net/api/text2image?prompt=woman%20hand%20wearing%20smartwatch%20with%20beaded%20band%20elegant%20minimal%20background&image_size=square_hd'
];

const StarIcon = ({ filled }) => (
	<svg xmlns="http://www.w3.org/2000/svg" width="23" height="22" viewBox="0 0 23 22" fill="none">
		<path
			d="M11.4126 0L14.1068 8.2918H22.8253L15.7719 13.4164L18.466 21.7082L11.4126 16.5836L4.35917 21.7082L7.05334 13.4164L-8.01086e-05 8.2918H8.71843L11.4126 0Z"
			fill={filled ? '#CD3D1C' : '#D9D9D9'}
		/>
	</svg>
);

export default function Edit({ attributes, setAttributes }) {
	const { 
		title, 
		subtitle,
		orderNumberLabel,
		orderNumberPlaceholder,
		orderHelpLink,
		orderHelpTooltip,
		emailLabel,
		nameLabel,
		namePlaceholder,
		countryLabel,
		optionalLabel,
		experienceRatingLabel,
		ratingQuestion,
		checkboxText,
		submitButtonText,
		noOrderLinkText,
		noOrderLinkUrl,
		modalTitle,
		modalMessage,
		modalDescription,
		modalWhatsappText,
		modalFacebookText,
		modalBenefitsNote,
		errorModalTitle,
		errorModalMessage,
		productImage1,
		productImage2,
		productImage3,
		productImage4,
		productImage5,
		productImage6,
		productImage7,
		productImage8,
		productImage9,
		productImage10
	} = attributes;
	const blockProps = useBlockProps();

	const pages = useSelect( function ( select ) {
		const { getEntityRecords } = select( 'core' );
		const records = getEntityRecords( 'postType', 'page', {
			per_page: -1,
			orderby: 'title',
			order: 'asc',
			status: 'publish',
		} );
		if ( ! records ) {
			return [];
		}
		return records.map( function ( page ) {
			return {
				value: page.link || '',
				label: page.title.rendered || page.title.raw || __( '(无标题)' ),
			};
		} );
	}, [] );

	return (
		<div { ...blockProps }>
			<InspectorControls>
			<PanelBody title="表单设置">
				<TextControl
					label="标题"
					value={ title }
					onChange={ ( value ) => setAttributes( { title: value } ) }
				/>
				<TextControl
					label="副标题"
					value={ subtitle }
					onChange={ ( value ) => setAttributes( { subtitle: value } ) }
				/>
				<TextControl
					label="订单号标签"
					value={ orderNumberLabel }
					onChange={ ( value ) => setAttributes( { orderNumberLabel: value } ) }
				/>
				<TextControl
					label="订单号占位符"
					value={ orderNumberPlaceholder }
					onChange={ ( value ) => setAttributes( { orderNumberPlaceholder: value } ) }
				/>
				<TextControl
					label="订单帮助链接文字"
					value={ orderHelpLink }
					onChange={ ( value ) => setAttributes( { orderHelpLink: value } ) }
				/>
				<TextareaControl
					label="订单帮助提示文字"
					value={ orderHelpTooltip }
					onChange={ ( value ) => setAttributes( { orderHelpTooltip: value } ) }
				/>
				<TextControl
					label="邮箱标签"
					value={ emailLabel }
					onChange={ ( value ) => setAttributes( { emailLabel: value } ) }
				/>
				<TextControl
					label="姓名标签"
					value={ nameLabel }
					onChange={ ( value ) => setAttributes( { nameLabel: value } ) }
				/>
				<TextControl
					label="姓名占位符"
					value={ namePlaceholder }
					onChange={ ( value ) => setAttributes( { namePlaceholder: value } ) }
				/>
				<TextControl
					label="国家标签"
					value={ countryLabel }
					onChange={ ( value ) => setAttributes( { countryLabel: value } ) }
				/>
				<TextControl
					label="可选标签"
					value={ optionalLabel }
					onChange={ ( value ) => setAttributes( { optionalLabel: value } ) }
				/>
				<TextControl
					label="体验评分标签"
					value={ experienceRatingLabel }
					onChange={ ( value ) => setAttributes( { experienceRatingLabel: value } ) }
				/>
				<TextControl
					label="评分问题"
					value={ ratingQuestion }
					onChange={ ( value ) => setAttributes( { ratingQuestion: value } ) }
				/>
				<TextareaControl
					label="复选框文字"
					value={ checkboxText }
					onChange={ ( value ) => setAttributes( { checkboxText: value } ) }
				/>
				<TextControl
					label="提交按钮文字"
					value={ submitButtonText }
					onChange={ ( value ) => setAttributes( { submitButtonText: value } ) }
				/>
				<TextControl
					label="无订单号链接文字"
					value={ noOrderLinkText }
					onChange={ ( value ) => setAttributes( { noOrderLinkText: value } ) }
				/>
				<div style={{ marginBottom: '24px' }}>
					<label style={{ display: 'block', marginBottom: '8px', fontWeight: 500, fontSize: '13px' }}>无订单号链接页面</label>
					<SelectControl
						value={ noOrderLinkUrl }
						options={ [
							{ value: '', label: __( '-- 请选择页面 --' ) },
							...pages,
						] }
						onChange={ ( value ) => setAttributes( { noOrderLinkUrl: value } ) }
					/>
				</div>
			</PanelBody>
			<PanelBody title="成功弹框设置">
				<TextControl
					label="弹框标题"
					value={ modalTitle }
					onChange={ ( value ) => setAttributes( { modalTitle: value } ) }
				/>
				<TextControl
					label="弹框消息"
					value={ modalMessage }
					onChange={ ( value ) => setAttributes( { modalMessage: value } ) }
				/>
				<TextareaControl
					label="弹框描述"
					value={ modalDescription }
					onChange={ ( value ) => setAttributes( { modalDescription: value } ) }
				/>
				<TextControl
					label="WhatsApp 文字"
					value={ modalWhatsappText }
					onChange={ ( value ) => setAttributes( { modalWhatsappText: value } ) }
				/>
				<TextControl
					label="Facebook 文字"
					value={ modalFacebookText }
					onChange={ ( value ) => setAttributes( { modalFacebookText: value } ) }
				/>
				<TextControl
					label="福利说明"
					value={ modalBenefitsNote }
					onChange={ ( value ) => setAttributes( { modalBenefitsNote: value } ) }
				/>
			</PanelBody>
			<PanelBody title="失败弹框设置">
				<TextControl
					label="失败弹框标题"
					value={ errorModalTitle }
					onChange={ ( value ) => setAttributes( { errorModalTitle: value } ) }
				/>
				<TextareaControl
					label="失败弹框内容"
					value={ errorModalMessage }
					onChange={ ( value ) => setAttributes( { errorModalMessage: value } ) }
				/>
			</PanelBody>
			<PanelBody title="产品图片设置（10张）">
				{[
					{ attr: 'productImage1', label: '产品图片 1（左上小图）', index: 0 },
					{ attr: 'productImage2', label: '产品图片 2（左列大图）', index: 1 },
					{ attr: 'productImage3', label: '产品图片 3（左下小图）', index: 2 },
					{ attr: 'productImage4', label: '产品图片 4（中间左上）', index: 3 },
					{ attr: 'productImage5', label: '产品图片 5（中间右上）', index: 4 },
					{ attr: 'productImage6', label: '产品图片 6（中间左下）', index: 5 },
					{ attr: 'productImage7', label: '产品图片 7（中间右下）', index: 6 },
					{ attr: 'productImage8', label: '产品图片 8（右上小图）', index: 7 },
					{ attr: 'productImage9', label: '产品图片 9（右上小图）', index: 8 },
					{ attr: 'productImage10', label: '产品图片 10（右列大图）', index: 9 },
				].map( ( { attr, label } ) => {
					const imageUrl = attributes[ attr ];
					return (
						<div key={ attr } style={ { marginBottom: '12px' } }>
							<label style={ { display: 'block', marginBottom: '8px', fontWeight: '500' } }>
								{ label }
							</label>
							<div style={ { display: 'flex', gap: '8px', alignItems: 'center' } }>
								{ imageUrl && (
									<img
										src={ imageUrl }
										alt=""
										style={ { width: '60px', height: '60px', objectFit: 'cover', borderRadius: '4px' } }
									/>
								) }
								<MediaUploadCheck>
									<MediaUpload
										onSelect={ ( media ) => {
											const url = media.url || ( media.sizes && media.sizes.full && media.sizes.full.url ) || '';
											setAttributes( { [ attr ]: url } );
										} }
										allowedTypes={ [ 'image' ] }
										render={ ( { open } ) => (
											<Button onClick={ open } variant="secondary">
												{ imageUrl ? '更换图片' : '选择图片' }
											</Button>
										) }
									/>
								</MediaUploadCheck>
								{ imageUrl && (
									<Button
										onClick={ () => setAttributes( { [ attr ]: '' } ) }
										variant="link"
										isDestructive
									>
										移除
									</Button>
								) }
							</div>
						</div>
					);
				} )}
			</PanelBody>
		</InspectorControls>

			<div className="warranty-application">
				<h2 className="warranty-application__title">{ title }</h2>
				<p className="warranty-application__subtitle">{ subtitle }</p>
				<div className="warranty-application__form">
					<div className="form-group">
						<label>{orderNumberLabel} <span className="required">*</span></label>
						<div className="form-content">
							<input type="text" placeholder={orderNumberPlaceholder} />
							<a href="#" className="order-help-link">{orderHelpLink}</a>
						</div>
					</div>
					<div className="form-group">
						<label>{emailLabel} <span className="required">*</span></label>
						<div className="form-content">
							<input type="email" placeholder="" />
						</div>
					</div>
					<div className="form-group">
						<label>{nameLabel}</label>
						<div className="form-content">
							<input type="text" placeholder={namePlaceholder} />
						</div>
					</div>
					<div className="form-group">
						<label>{countryLabel}</label>
						<div className="form-content">
							<input type="text" placeholder="" />
						</div>
					</div>
					<div className="form-group">
						<label>{optionalLabel}</label>
						<div className="form-content">
							<div className="rating-section">
								<span>{experienceRatingLabel}</span>
								<div className="stars">
									<span className="star">
										<StarIcon filled={false} />
									</span>
									<span className="star">
										<StarIcon filled={false} />
									</span>
									<span className="star">
										<StarIcon filled={false} />
									</span>
									<span className="star">
										<StarIcon filled={false} />
									</span>
									<span className="star">
										<StarIcon filled={false} />
									</span>
								</div>
							</div>
							<div className="rating-labels">
								<p>{ratingQuestion}</p>
							</div>
						</div>
					</div>
					<div className="form-group">
						<label></label>
						<div className="form-content">
							<div className='text-box'>
								<input className='input-box' type="checkbox" />
								{checkboxText}
							</div>
						</div>
					</div>
					<button className="submit-btn">{submitButtonText}</button>
					<a href={noOrderLinkUrl} className="no-order-link">{noOrderLinkText} <span className="dropdown-arrow">▼</span></a>
				</div>
			</div>
		</div>
	);
}
