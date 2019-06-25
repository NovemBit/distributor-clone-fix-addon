/**
 * Handle frontend actions
 *
 * @package distributor-clone-fix
 */

document.addEventListener('DOMContentLoaded', () => {
	/**
	 * Listen to apply button
	 */
	document.getElementById('doaction').addEventListener('click', e => {
		const select = document.getElementById('bulk-action-selector-top');
		if (select.value === 'clone_fix') {
			submitFix(e);
		}
	});

	/**
	 * Handle bottom button too
	 */
	document.getElementById('doaction2').addEventListener('click', e => {
		const select = document.getElementById('bulk-action-selector-bottom');
		if (select.value === 'clone_fix') {
			document.getElementById('bulk-action-selector-top').value =
				'clone_fix';
			submitFix(e);
		}
	});

	/**
	 * Remove added fields if selection is not clone fix
	 */
	document
		.getElementById('bulk-action-selector-top')
		.addEventListener('change', e => {
			if (inputCreated() && e.target.value !== 'clone_fix') {
				removeConnectionInput();
			}
		});

	/**
	 * Process bulk fix
	 * @param {Event} e
	 */
	function submitFix(e) {
		e.preventDefault();
		if (!inputCreated()) {
			addConnectionInput();
			return;
		}
		if (!validatePostSelection()) {
			alert('No posts selected!');
			return;
		}
		if (!validateConnectionSelection()) {
			alert('Please select connection to fix');
			highlightSelect();
			return;
		}

		const data = new FormData();
		data.append('nonce', window.cloneFixData.nonce);
		data.append('action', 'fix_clones');
		data.append('posts', getSelectedPosts());
		data.append(
			'connection',
			document.getElementById('dt_fix_connection_id').value
		);
		const http = new window.XMLHttpRequest();
		http.onreadystatechange = function() {
			if (http.readyState == 4 && http.status == 200) {
				console.log(http.responseText);
			}
		};
		http.open('post', window.ajaxurl);
		http.send(data);
	}

	/**
	 * Get slected posts for buk fixing
	 *
	 * @returns {Array}
	 */
	function getSelectedPosts() {
		let result = [];
		const checkboxes = document.querySelectorAll(
			'input[name="post[]"][type="checkbox"]'
		);
		for (let i in checkboxes) {
			if (Object.prototype.hasOwnProperty.call(checkboxes, i)) {
				if (checkboxes[i].checked) {
					result.push(checkboxes[i].value);
				}
			}
		}
		return result;
	}

	/**
	 * Check if connection input created
	 *
	 * @returns {Boolean}
	 */
	function inputCreated() {
		return !empty(document.getElementById('dt_fix_connection_id'));
	}

	function validateConnectionSelection() {
		return document.getElementById('dt_fix_connection_id').value !== '';
	}

	/**
	 * Check if post selected for bulk fixing
	 *
	 * @returns {Boolean}
	 */
	function validatePostSelection() {
		let result = false;
		const checkboxes = document.querySelectorAll(
			'input[name="post[]"][type="checkbox"]'
		);
		for (let i in checkboxes) {
			if (Object.prototype.hasOwnProperty.call(checkboxes, i)) {
				if (checkboxes[i].checked) {
					result = true;
					break;
				}
			}
		}
		return result;
	}

	/**
	 * Helper function to check if simple var is empty (Not checks Arrays, Objects, HTMLCollections etc.)
	 * @param {*} value
	 */
	function empty(value) {
		return (
			typeof value === 'undefined' ||
			value === undefined ||
			value === null ||
			value === false
		);
	}

	/**
	 * Insert connection select box into DOM
	 */
	function addConnectionInput() {
		const { connections } = window.cloneFixData;
		if (typeof connections === 'object') {
			if (Object.keys(connections).length === 0) {
				alert('You have no connections created');
			} else {
				const select = document.createElement('select');
				select.id = 'dt_fix_connection_id';
				select.name = 'connection';
				const button = document.getElementById('doaction');
				button.parentElement.insertBefore(select, button);
				const placeholder = document.createElement('option');
				placeholder.value = '';
				placeholder.text = 'Select External Connection';
				select.appendChild(placeholder);
				for (let i in connections) {
					if (Object.prototype.hasOwnProperty.call(connections, i)) {
						const option = document.createElement('option');
						option.value = connections[i].id;
						option.text = connections[i].title;
						select.appendChild(option);
					}
				}
			}
		}
	}

	/**
	 * Highlight select box on notSelected error
	 */
	function highlightSelect() {
		const select = document.getElementById('dt_fix_connection_id');
		select.scrollIntoView({ block: 'center' });
		select.style.borderColor = 'red';
		setTimeout(() => {
			select.style.borderColor = null;
		}, 2000);
	}
	/**
	 * Remove inserted selecttbox from DOM
	 */
	function removeConnectionInput() {
		const select = document.getElementById('dt_fix_connection_id');
		if (!empty(select)) {
			select.parentElement.removeChild(select);
		}
	}
});
