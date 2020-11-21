import {EventsHelper} from './EventsHelper.js';
import { Portal } from './Portal.js';
import { PortalsViewModel } from './PortalsViewModel.js';

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
			document.body.classList.toggle('hidden-zoomer');
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
		this.mainFields.done.addEventListener('change', ()=>{
			if (this.mainForm._zoomerImg instanceof Element) {
				let done = this.mainFields.done.checked;
				this.portalsViewModel.changeDoneState(this.mainForm._zoomerImg, done);
			}
		});
		this.mainFields.notes.addEventListener('change', ()=>{
			if (this.mainForm._portal instanceof Portal) {
				let portal = this.mainForm._portal;
				portal.notes = this.mainFields.notes.value;
				this.portalsViewModel.changePortalState(portal);
			}
		});
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
		// fill fields
		this.mainForm._zoomerImg = img;
		this.mainForm._portal = portal;
		this.mainFields.done.checked = portal.done;
		$(this.mainFields.done).checkboxradio("refresh");
		this.mainFields.puzzle.value = portal.puzzleData();
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
	 * Append or remove image from zoomer list.
	 * @private
	 * @param {Element} img Column image.
	 */
	cellToggle(img) {
		const id = img.id;
		if (this.idList.has(id)) {
			this.idList.delete(id);
			let el = this.listContainer.querySelector(`.list.${id}`);
			if (el) {
				this.listContainer.removeChild(el);
			} else {
				console.warn(`Zoomer list element not found ${id}`);
			}
			img.classList.remove('zoomer-list');
		} else {
			img.classList.add('zoomer-list');
			this.idList.add(id);
			let nel = document.createElement('figure');
			nel.innerHTML = `<img src='${img.src}'><figcaption>${img.title}</figcaption>`;
			nel.className = `list ${id}`;
			this.listContainer.appendChild(nel);

			// allow zoom-in from list
			nel.setAttribute('data-id', id);
			EventsHelper.clickDraggable(nel, () => {
				let id = nel.getAttribute('data-id');
				let img = document.getElementById(id);
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