import {EventsHelper} from './EventsHelper.js';
import { Portal } from './Portal.js';
import { PortalsViewModel } from './PortalsViewModel.js';
import { ClipboardHelper } from './ClipboardHelper.js';

/**
 * Zoomer view handler.
 */
class ZoomerViewModel {
	constructor() {
		/**
		 * Zoomer main container element.
		 */
		this.mainContainer = null;
		/**
		 * Zoomer img list container element.
		 */
		this.listContainer = null;
		/**
		 * Id set of the img list.
		 */
		this.idList = new Set();

		this.portalsViewModel = null;
	}

	/**
	 * Init interactions.
	 * @param {PortalsViewModel} portalsViewModel VM for portal ops.
	 */
	init(portalsViewModel) {
		this.portalsViewModel = portalsViewModel;
		this.mainContainer = document.querySelector('#zoomer');
		this.listContainer = document.querySelector('#zoomer-list');
		this.initZoom();

		document.querySelector('#toggle-zoomer').addEventListener('click', ()=>{
			document.body.classList.remove('right-zoomer');
			document.body.classList.toggle('hidden-zoomer');
		});
		document.querySelector('#toggle-right-zoomer').addEventListener('click', ()=>{
			document.body.classList.remove('hidden-zoomer');
			document.body.classList.toggle('right-zoomer');
		});

		this.initList();
	}

	/**
	 * Init list elements.
	 * @private
	 */
	initList() {
		let controls = document.querySelector('#zoomer-list-controls');
		this.initListClear(controls);
		this.initListResize(controls);
		this.listControls = controls;
		this.listControls.style.display = 'none';

		document.querySelector('.zoomer-hide').addEventListener('click', ()=>{
			this.mainSection.style.display = 'none';
		});
	}

	/**
	 * Enlarge image.
	 * @private
	 */
	initZoom() {
		this.mainSection = document.querySelector('#zoomer .main');
		this.mainImg = this.mainSection.querySelector('img');
		this.mainCaption = this.mainSection.querySelector('figcaption');
		this.initForm();
		document.querySelectorAll('.column img').forEach(img=>{
			img.addEventListener('click', (e)=>{
				if (!e.ctrlKey) {
					// single/main image show
					this.cellLoad(img);
				} else {
					// add/remove from list
					this.mainSection.style.display = 'none';
					this.cellToggle(img);
				}

				// visibility of controls
				if (this.idList.size < 1) {
					this.listControls.style.display = 'none';
				} else {
					this.listControls.style.display = '';
				}
			});
		});	
	}

	/**
	 * Init main edit form.
	 */
	initForm() {
		this.mainForm = this.mainSection.querySelector('#cell-form');
		this.mainFields = {
			done: this.mainForm.querySelector('[name="done"]'),
			puzzle: this.mainForm.querySelector('[name="puzzle"]'),
			notes: this.mainForm.querySelector('[name="notes"]'),
		}

		// changes
		this.mainFields.done.addEventListener('change', ()=>{
			if (this.mainForm._zoomerImg instanceof Element) {
				let done = this.mainFields.done.checked;
				this.portalsViewModel.changeDoneState(this.mainForm._zoomerImg, done);
				this.updateListCell(this.mainForm._zoomerImg.id, done);
			}
		});
		this.mainFields.notes.addEventListener('change', ()=>{
			if (this.mainForm._portal instanceof Portal) {
				let portal = this.mainForm._portal;
				portal.notes = this.mainFields.notes.value;
				this.portalsViewModel.changePortalState(portal);
			}
		});
		this.mainFields.puzzle.addEventListener('change', ()=>{
			if (this.mainForm._portal instanceof Portal) {
				let portal = this.mainForm._portal;
				let wasDone = portal.done;
				const input = this.mainFields.puzzle;
				let valid = portal.setPuzzleData(input.value);
				if (!valid) {
					input.setCustomValidity('The text is invalid! Use FS helper.');
					input.reportValidity();
				} else {
					input.setCustomValidity('');
					input.reportValidity();
					this.portalsViewModel.changePortalState(portal);
					if (wasDone != portal.done) {
						this.portalsViewModel.changeDoneState(this.mainForm._zoomerImg, portal.done, true);
						this.updateDoneField(portal.done);
					}
				}
				this.updateListCell(portal.id, portal.done);
			}
		});
		
		// extras
		this.mainForm.querySelector('[for="zoomer_field_puzzle"')
			.addEventListener('click', ()=>{
				ClipboardHelper.copyTextField(this.mainFields.puzzle);
				console.log('copied');
			})
		;
	}

	/**
	 * Update state of cell in zoomer list.
	 * 
	 * @param {String} id Cell's id (same as image id).
	 * @param {boolean} done 
	 */
	updateListCell(id, done) {
		let el = this.listContainer.querySelector(`.list.${id}`);
		if (el) {
			if (done) {
				el.classList.add('done-cell');
			} else {
				el.classList.remove('done-cell');
			}
		}
	}

	/**
	 * Update done field for CURRENT portal.
	 * @param {boolean} done 
	 */
	updateDoneField(done) {
		this.mainFields.done.checked = done;
		$(this.mainFields.done).checkboxradio("refresh");
	}
	/**
	 * Update puzzle field for CURRENT portal.
	 * @param {Portal} portal.
	 */
	updatePuzzleField(portal) {
		this.mainFields.puzzle.value = portal.puzzleData();
		this.mainFields.puzzle.setCustomValidity('');
		this.mainFields.puzzle.reportValidity();
	}

	/**
	 * Load cell image to main view.
	 * @private
	 */
	cellLoad(img) {
		let portal = this.portalsViewModel.getPortal(img);
		
		// fill display data
		this.mainImg.src = img.src;
		this.mainCaption.textContent = img.title;
		this.mainSection.style.display = '';
		this.mainForm.style.display = '';

		// prep internals
		this.mainForm._zoomerImg = img;
		this.mainForm._portal = portal;

		// init fields
		this.updateDoneField(portal.done);
		this.updatePuzzleField(portal);
		this.mainFields.notes.value = portal.notes;
	}

	/**
	 * Init list clear.
	 * @private
	 * @param {Element} container Controls container.
	 */
	initListClear(container) {
		container.querySelector('.clear').addEventListener('click', ()=>{
			this.idList.clear();
			this.listControls.style.display = 'none';
			this.listContainer.querySelectorAll(`.list`).forEach(element => {
				this.listContainer.removeChild(element);
			});
			document.querySelectorAll('.column img.zoomer-list').forEach(img => {
				img.classList.remove('zoomer-list');
			});
		});
	}

	/**
	 * Remove image from zoomer list.
	 * @private
	 * @param {Element} img Column image.
	 * @param {String} img Column image id.
	 */
	cellRemove(img, id) {
		this.idList.delete(id);
		let el = this.listContainer.querySelector(`.list.${id}`);
		if (el) {
			this.listContainer.removeChild(el);
		} else {
			console.warn(`Zoomer list element not found ${id}`);
		}
		img.classList.remove('zoomer-list');
	}

	/**
	 * Append or remove image from zoomer list.
	 * @private
	 * @param {Element} img Column image.
	 */
	cellToggle(img) {
		const id = img.id;
		if (this.idList.has(id)) {
			this.cellRemove(img, id);
		} else {
			img.classList.add('zoomer-list');
			this.idList.add(id);
			let nel = document.createElement('figure');
			nel.innerHTML = `
				<button class="figure-close" title="remove from list">✕</button>
				<img src='${img.src}'>
				<figcaption>${img.title}</figcaption>
			`;
			nel.className = `list ${id}`;
			if (img.classList.contains('done-cell')) {
				nel.classList.add('done-cell');
			}
			this.listContainer.appendChild(nel);

			// close (remove from list)
			let close = nel.querySelector('.figure-close');
			close.addEventListener('click', () => {
				this.cellRemove(img, id);
			});

			// allow zoom-in from list
			let listImage = nel.querySelector('img');
			EventsHelper.clickDraggable(listImage, () => {
				this.cellLoad(img);
			});
		}
	}

	/**
	 * Init resizing buttons.
	 * @private
	 * @param {Element} container Controls container.
	 */
	initListResize(container) {
		const resizers = container.querySelectorAll('.resize');
		const classes = [...resizers].map(el=>el.getAttribute('data-class'));
		resizers.forEach(button => {
			button.addEventListener('click', ()=>{
				let resizeClass = button.getAttribute('data-class');
				this.mainContainer.classList.remove(...classes);
				this.mainContainer.classList.add(resizeClass);
			});
		})
	}

}

export { ZoomerViewModel }