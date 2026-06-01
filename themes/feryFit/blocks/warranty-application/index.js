import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import Edit from './edit';
import save from './save';

registerBlockType( 'feryfit/warranty-application', {
	edit: Edit,
	save,
} );