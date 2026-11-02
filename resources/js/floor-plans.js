import { apiMessage, apiRequest, escapeHtml, requireSession } from './nivora-api';

const SVG_NS = 'http://www.w3.org/2000/svg';

const PLAN_TYPE_OPTIONS = [
    { value: 'wall_rack', label: 'Rak Dinding' },
    { value: 'mid_rack', label: 'Rak Tengah' },
    { value: 'cashier_desk', label: 'Meja Kasir' },
    { value: 'door', label: 'Pintu Masuk' },
    { value: 'window', label: 'Jendela' },
    { value: 'stockroom', label: 'Gudang' },
    { value: 'storage', label: 'Area Penyimpanan' },
    { value: 'aisle', label: 'Lorong Pelanggan' },
];

const TYPE_LABELS = {
    wall_rack: 'Rak Dinding',
    mid_rack: 'Rak Tengah',
    cashier_desk: 'Meja Kasir',
    door: 'Pintu Masuk',
    window: 'Jendela',
    stockroom: 'Gudang',
    storage: 'Area Penyimpanan',
    aisle: 'Lorong Pelanggan',
};

function toNumber(value, fallback = 0) {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function parseAreaLine(line) {
    const [type, x, y, width, height] = line.split(',').map((part) => Number(part.trim()));
    return {
        type: String(type || 'storage'),
        x: Number.isFinite(x) ? x : 0,
        y: Number.isFinite(y) ? y : 0,
        width: Number.isFinite(width) ? width : 1,
        height: Number.isFinite(height) ? height : 1,
    };
}

export function bootFloorPlans() {
    document.querySelectorAll('[data-floor-plan]').forEach((root) => initializeFloorPlan(root));
}

function initializeFloorPlan(root) {
    if (!requireSession()) {
        return;
    }

    const state = {
        id: null,
        status: 'draft',
        roomLength: 0,
        roomWidth: 0,
        door: { x: 0, y: 0, width: 0.9, height: 2.1 },
        windows: [],
        permanentAreas: [],
        plan: [],
        analysis: null,
        zoom: 1,
        selectedObjectId: null,
        editMode: false,
    };

    const elements = {
        alert: root.querySelector('[data-floor-plan-message]'),
        generateForm: root.querySelector('[data-floor-plan-generate-form]'),
        generateButton: root.querySelector('[data-floor-plan-generate]'),
        roomLength: root.querySelector('[data-floor-plan-room-length]'),
        roomWidth: root.querySelector('[data-floor-plan-room-width]'),
        doorX: root.querySelector('[data-floor-plan-door-x]'),
        doorY: root.querySelector('[data-floor-plan-door-y]'),
        doorWidth: root.querySelector('[data-floor-plan-door-width]'),
        doorHeight: root.querySelector('[data-floor-plan-door-height]'),
        windows: root.querySelector('[data-floor-plan-windows]'),
        permanentAreas: root.querySelector('[data-floor-plan-permanent-areas]'),
        generateSummary: root.querySelector('[data-floor-plan-generate-summary]'),
        svg: root.querySelector('[data-floor-plan-svg]'),
        roomSize: root.querySelector('[data-floor-plan-room-size]'),
        zoomIn: root.querySelector('[data-floor-plan-zoom-in]'),
        zoomOut: root.querySelector('[data-floor-plan-zoom-out]'),
        zoomReset: root.querySelector('[data-floor-plan-zoom-reset]'),
        zoomValue: root.querySelector('[data-floor-plan-zoom-value]'),
        saveButton: root.querySelector('[data-floor-plan-save]'),
        downloadPng: root.querySelector('[data-floor-plan-download-png]'),
        downloadPdf: root.querySelector('[data-floor-plan-download-pdf]'),
        generateAgain: root.querySelector('[data-floor-plan-generate-again]'),
        editToggle: root.querySelector('[data-floor-plan-edit]'),
        tab2d: root.querySelector('[data-floor-plan-tab="2d"]'),
        tab3d: root.querySelector('[data-floor-plan-tab="3d"]'),
        panel2d: root.querySelector('[data-floor-plan-panel="2d"]'),
        panel3d: root.querySelector('[data-floor-plan-panel="3d"]'),
        objects: root.querySelector('[data-floor-plan-objects]'),
        addType: root.querySelector('[data-floor-plan-add-type]'),
        addButton: root.querySelector('[data-floor-plan-add]'),
        selectedDetails: root.querySelector('[data-floor-plan-selected]'),
        selectedName: root.querySelector('[data-floor-plan-selected-name]'),
        selectedType: root.querySelector('[data-floor-plan-selected-type]'),
        selectedX: root.querySelector('[data-floor-plan-selected-x]'),
        selectedY: root.querySelector('[data-floor-plan-selected-y]'),
        selectedWidth: root.querySelector('[data-floor-plan-selected-width]'),
        selectedHeight: root.querySelector('[data-floor-plan-selected-height]'),
    };

    const setMessage = (message, isError = true) => {
        const alert = elements.alert;
        if (!alert) {
            return;
        }

        alert.textContent = message;
        alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700'}`;
    };

    const loadLatestPlan = async () => {
        try {
            const response = await apiRequest('/ai/floor-plans');
            const plans = response?.data?.data || response?.data?.plans || [];
            const plan = plans[0];

            if (!plan) {
                return;
            }

            state.id = plan.id ?? null;
            state.status = plan.status || 'draft';
            state.roomLength = toNumber(plan.room_length_m);
            state.roomWidth = toNumber(plan.room_width_m);
            state.door = { ...state.door, ...(plan.door ?? plan.input_summary?.door ?? {}) };
            state.windows = plan.windows ?? plan.input_summary?.windows ?? [];
            state.permanentAreas = plan.permanentAreas ?? plan.input_summary?.permanent_areas ?? [];
            state.plan = plan.plan ?? [];
            state.analysis = plan.analysis_result ?? null;
            state.editMode = false;

            if (elements.roomLength) elements.roomLength.value = String(state.roomLength);
            if (elements.roomWidth) elements.roomWidth.value = String(state.roomWidth);
            if (elements.doorX) elements.doorX.value = String(state.door.x);
            if (elements.doorY) elements.doorY.value = String(state.door.y);
            if (elements.doorWidth) elements.doorWidth.value = String(state.door.width);
            if (elements.doorHeight) elements.doorHeight.value = String(state.door.height ?? 2.1);

            if (elements.roomSize) {
                elements.roomSize.textContent = `${state.roomLength} m x ${state.roomWidth} m`;
            }

            render();
        } catch (error) {
            setMessage(apiMessage(error));
        }
    };

    const buildInput = () => ({
        room_length_m: toNumber(elements.roomLength?.value, 0),
        room_width_m: toNumber(elements.roomWidth?.value, 0),
        door: {
            x: toNumber(elements.doorX?.value, 0),
            y: toNumber(elements.doorY?.value, 0),
            width: toNumber(elements.doorWidth?.value, 0.9),
            height: toNumber(elements.doorHeight?.value, 2.1),
        },
        windows: parseWindows(elements.windows?.value),
        permanent_areas: parseAreas(elements.permanentAreas?.value),
    });

    const parseWindows = (value) => {
        const lines = String(value || '').split('\n');
        const windows = [];

        lines.forEach((line) => {
            const trimmed = line.trim();
            if (!trimmed) {
                return;
            }

            const parsed = parseAreaLine(trimmed);
            if (parsed.width > 0) {
                windows.push(parsed);
            }
        });

        return windows;
    };

    const parseAreas = (value) => {
        const lines = String(value || '').split('\n');
        const areas = [];

        lines.forEach((line) => {
            const trimmed = line.trim();
            if (!trimmed) {
                return;
            }

            const parsed = parseAreaLine(trimmed);
            if (parsed.width > 0) {
                areas.push(parsed);
            }
        });

        return areas;
    };

    const generate = async () => {
        if (!elements.generateButton) {
            return;
        }

        const input = buildInput();
        if (input.room_length_m < 0.5 || input.room_width_m < 0.5 || input.door.x < 0 || input.door.y < 0) {
            setMessage('Dimensi ruangan dan posisi pintu masuk wajib diisi sebelum denah dapat digenerate.');
            return;
        }

        elements.generateButton.disabled = true;
        elements.generateButton.textContent = 'Memproses…';

        try {
            const response = await apiRequest('/ai/floor-plans', {
                method: 'POST',
                body: JSON.stringify(input),
            });
            const plan = response?.data?.data;
            Object.assign(state, {
                id: plan?.id ?? null,
                status: plan?.status ?? 'draft',
                roomLength: toNumber(plan?.room_length_m, input.room_length_m),
                roomWidth: toNumber(plan?.room_width_m, input.room_width_m),
                door: plan?.door ?? input.door,
                windows: plan?.windows ?? input.windows,
                permanentAreas: plan?.permanent_areas ?? input.permanent_areas,
                plan: plan?.plan ?? [],
                analysis: plan?.analysis_result ?? null,
                editMode: false,
            });

            if (elements.roomLength) elements.roomLength.value = String(state.roomLength);
            if (elements.roomWidth) elements.roomWidth.value = String(state.roomWidth);
            if (elements.roomSize) elements.roomSize.textContent = `${state.roomLength} m x ${state.roomWidth} m`;

            setMessage(response?.message || 'Denah 2D berhasil digenerate.', false);
            render();
        } catch (error) {
            setMessage(apiMessage(error));
        } finally {
            elements.generateButton.disabled = false;
            elements.generateButton.textContent = 'Generate Denah';
        }
    };

    const savePlan = async () => {
        if (!state.id || !elements.saveButton) {
            return;
        }

        elements.saveButton.disabled = true;
        elements.saveButton.textContent = 'Menyimpan…';

        try {
            const response = await apiRequest(`/ai/floor-plans/${state.id}`, {
                method: 'PUT',
                body: JSON.stringify({ plan: state.plan }),
            });
            state.status = response?.data?.data?.status ?? 'saved';
            setMessage(response?.message || 'Denah berhasil disimpan.', false);
        } catch (error) {
            setMessage(apiMessage(error));
        } finally {
            elements.saveButton.disabled = false;
            elements.saveButton.textContent = 'Simpan Denah';
        }
    };

    const generateAgain = async () => {
        if (elements.generateAgain) {
            elements.generateAgain.disabled = true;
        }

        try {
            await generate();
        } finally {
            if (elements.generateAgain) {
                elements.generateAgain.disabled = false;
            }
        }
    };

    const renderFloorPlan = (svg, currentState, options = {}) => {
        if (!svg) {
            return;
        }

        const zoom = options.zoom ?? currentState.zoom;
        const length = currentState.roomLength;
        const width = currentState.roomWidth;
        svg.setAttribute('viewBox', `0 0 ${length} ${width}`);
        const viewport = document.createElementNS(SVG_NS, 'g');
        viewport.setAttribute('transform', `scale(${zoom})`);

        const room = document.createElementNS(SVG_NS, 'rect');
        room.setAttribute('x', '0');
        room.setAttribute('y', '0');
        room.setAttribute('width', String(length));
        room.setAttribute('height', String(width));
        room.setAttribute('class', 'floor-plan-room');
        viewport.append(room);

        currentState.plan.forEach((object) => {
            const rect = document.createElementNS(SVG_NS, 'rect');
            const rotation = toNumber(object.rotation, 0);
            const swapped = rotation === 90 || rotation === 180 || rotation === 270;
            const widthValue = swapped ? toNumber(object.height, 0) : toNumber(object.width, 0);
            const heightValue = swapped ? toNumber(object.width, 0) : toNumber(object.height, 0);

            rect.setAttribute('x', String(toNumber(object.x, 0)));
            rect.setAttribute('y', String(toNumber(object.y, 0)));
            rect.setAttribute('width', String(widthValue));
            rect.setAttribute('height', String(heightValue));
            rect.setAttribute('class', 'floor-plan-object');
            rect.setAttribute('data-object-id', String(object.id ?? ''));
            rect.setAttribute('data-object-type', String(object.type ?? ''));
            rect.setAttribute('fill', String(object.color || '#687EFF'));
            rect.setAttribute('stroke', '#1F2544');
            rect.setAttribute('stroke-width', '0.02');
            viewport.append(rect);

            const label = document.createElementNS(SVG_NS, 'text');
            label.setAttribute('x', String(toNumber(object.x, 0) + widthValue / 2));
            label.setAttribute('y', String(toNumber(object.y, 0) + heightValue / 2));
            label.setAttribute('text-anchor', 'middle');
            label.setAttribute('dominant-baseline', 'central');
            label.setAttribute('class', 'floor-plan-object-label');
            label.textContent = String(object.name || object.type || '');
            viewport.append(label);
        });

        svg.innerHTML = '';
        svg.append(viewport);
    };

    const renderAnalysis = (container, currentState) => {
        if (!container) {
            return;
        }

        container.innerHTML = '';

        currentState.plan.forEach((object) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.setAttribute('data-object-id', String(object.id ?? ''));
            button.setAttribute('data-object-type', String(object.type ?? ''));
            button.className = 'floor-plan-object-chip';
            button.innerHTML = `<span class="floor-plan-object-dot" style="background-color: ${object.color}"></span>${escapeHtml(object.name || object.type)}`;

            if (String(object.id) === String(currentState.selectedObjectId)) {
                button.classList.add('is-selected');
            }

            button.addEventListener('click', () => {
                currentState.selectedObjectId = object.id;
                renderAnalysis(container, currentState);

                if (elements.selectedName) elements.selectedName.textContent = String(object.name || object.type);
                if (elements.selectedType) elements.selectedType.textContent = String(object.type);
                if (elements.selectedX) elements.selectedX.value = String(toNumber(object.x, 0));
                if (elements.selectedY) elements.selectedY.value = String(toNumber(object.y, 0));
                if (elements.selectedWidth) elements.selectedWidth.value = String(toNumber(object.width, 0));
                if (elements.selectedHeight) elements.selectedHeight.value = String(toNumber(object.height, 0));
            });

            container.append(button);
        });
    };

    const setupSvgInteractions = (svg, currentState) => {
        const pointFromEvent = (event) => {
            const matrix = svg.getScreenCTM();
            if (!matrix) {
                return { x: 0, y: 0 };
            }

            const point = new DOMPoint(event.clientX, event.clientY).matrixTransform(matrix.inverse());
            return { x: point.x, y: point.y };
        };

        const findObject = (x, y) => {
            const candidates = Array.from(svg.querySelectorAll('[data-object-id]')).map((element) => {
                const object = currentState.plan.find((item) => String(item.id) === element.getAttribute('data-object-id'));
                const rotation = toNumber(object?.rotation, 0);
                const swapped = rotation === 90 || rotation === 180 || rotation === 270;
                const widthValue = swapped ? toNumber(object?.height, 0) : toNumber(object?.width, 0);
                const heightValue = swapped ? toNumber(object?.width, 0) : toNumber(object?.height, 0);

                return {
                    element,
                    object,
                    widthValue,
                    heightValue,
                    contains: x >= toNumber(object?.x, 0) && x <= toNumber(object?.x, 0) + widthValue && y >= toNumber(object?.y, 0) && y <= toNumber(object?.y, 0) + heightValue,
                };
            }).filter((candidate) => candidate.contains);

            return candidates[0] || null;
        };

        const startDrag = (event, candidate) => {
            if (!currentState.editMode) {
                return;
            }

            event.preventDefault();
            currentState.selectedObjectId = candidate.element.getAttribute('data-object-id');
            renderAnalysis(elements.objects, currentState);
            const start = pointFromEvent(event);
            const startX = toNumber(candidate.object.x, 0);
            const startY = toNumber(candidate.object.y, 0);

            const onMove = (moveEvent) => {
                const point = pointFromEvent(moveEvent);
                const nextX = round2(startX + (point.x - start.x));
                const nextY = round2(startY + (point.y - start.y));

                candidate.object.x = Math.max(0, nextX);
                candidate.object.y = Math.max(0, nextY);

                render();
                renderAnalysis(elements.objects, currentState);
            };

            const onEnd = () => {
                window.removeEventListener('pointermove', onMove);
                window.removeEventListener('pointerup', onEnd);
            };

            window.addEventListener('pointermove', onMove);
            window.addEventListener('pointerup', onEnd, { once: true });
        };

        svg.addEventListener('pointerdown', (event) => {
            const candidate = findObject(pointFromEvent(event).x, pointFromEvent(event).y);
            if (candidate) {
                startDrag(event, candidate);
            }
        });
    };

    const render3d = (svg, currentState) => {
        if (!svg) {
            return;
        }

        svg.innerHTML = '';
        const length = currentState.roomLength;
        const width = currentState.roomWidth;
        const group = document.createElementNS(SVG_NS, 'g');
        group.setAttribute('transform', `scale(0.85) translate(${20 + width * 0.45}, ${20 + length * 0.45})`);

        const room = document.createElementNS(SVG_NS, 'rect');
        room.setAttribute('x', String(-length / 2));
        room.setAttribute('y', String(-width / 2));
        room.setAttribute('width', String(length));
        room.setAttribute('height', String(width));
        room.setAttribute('class', 'floor-plan-room');
        group.append(room);

        currentState.plan.forEach((object) => {
            const rect = document.createElementNS(SVG_NS, 'rect');
            const swapped = toNumber(object.rotation, 0) === 90 || toNumber(object.rotation, 0) === 180 || toNumber(object.rotation, 0) === 270;
            const widthValue = swapped ? toNumber(object.height, 0) : toNumber(object.width, 0);
            const heightValue = swapped ? toNumber(object.width, 0) : toNumber(object.height, 0);

            rect.setAttribute('x', String(-length / 2 + toNumber(object.x, 0)));
            rect.setAttribute('y', String(-width / 2 + toNumber(object.y, 0)));
            rect.setAttribute('width', String(widthValue));
            rect.setAttribute('height', String(heightValue));
            rect.setAttribute('class', 'floor-plan-object');
            rect.setAttribute('fill', String(object.color || '#687EFF'));
            rect.setAttribute('stroke', '#1F2544');
            rect.setAttribute('stroke-width', '0.02');
            group.append(rect);
        });

        const before = document.createElementNS(SVG_NS, 'rect');
        before.setAttribute('x', String(-length / 2 - 2));
        before.setAttribute('y', String(-width / 2 - 2));
        before.setAttribute('width', String(length + 4));
        before.setAttribute('height', String(width + 4));
        before.setAttribute('class', 'floor-plan-room floor-plan-room-before');
        group.append(before);

        svg.append(group);
    };

    const render = () => {
        renderFloorPlan(elements.svg, state, { zoom: state.zoom });
        renderAnalysis(elements.objects, state);
        render3d(elements.querySelector('[data-floor-plan-3d]'), state);
    };

    const updateZoomLabel = () => {
        if (!elements.zoomValue) {
            return;
        }

        elements.zoomValue.textContent = `${Math.round((state.zoom || 1) * 100)}%`;
    };

    const round2 = (value) => Math.round(value * 100) / 100;

    // Event bindings.
    elements.generateForm?.addEventListener('submit', (event) => {
        event.preventDefault();
        generate();
    });

    elements.generateAgain?.addEventListener('click', generateAgain);
    elements.saveButton?.addEventListener('click', savePlan);
    elements.zoomIn?.addEventListener('click', () => {
        state.zoom = Math.min(2, round2((state.zoom || 1) * 1.25));
        updateZoomLabel();
        render();
    });
    elements.zoomOut?.addEventListener('click', () => {
        state.zoom = Math.max(0.5, round2((state.zoom || 1) * 0.8));
        updateZoomLabel();
        render();
    });
    elements.zoomReset?.addEventListener('click', () => {
        state.zoom = 1;
        updateZoomLabel();
        render();
    });
    elements.downloadPng?.addEventListener('click', downloadPng);
    elements.downloadPdf?.addEventListener('click', downloadPdf);
    elements.editToggle?.addEventListener('click', () => {
        state.editMode = ! state.editMode;
        elements.editToggle.setAttribute('aria-pressed', String(state.editMode));
        render();
    });
    elements.addButton?.addEventListener('click', addObject);
    elements.selectedDetails?.addEventListener('click', (event) => {
        const rotateButton = event.target.closest('[data-object-rotate]');
        const deleteButton = event.target.closest('[data-object-delete]');

        if (rotateButton) {
            rotateSelected();
            return;
        }

        if (deleteButton) {
            deleteSelected();
        }
    });
    elements.tab2d?.addEventListener('click', () => switchTab('2d'));
    elements.tab3d?.addEventListener('click', () => switchTab('3d'));

    if (elements.svg) {
        setupSvgInteractions(elements.svg, state);
    }

    const switchTab = (tab) => {
        if (elements.panel2d) elements.panel2d.classList.toggle('hidden', tab !== '2d');
        if (elements.panel3d) elements.panel3d.classList.toggle('hidden', tab !== '3d');
        if (elements.tab2d) elements.tab2d.classList.toggle('data-active', tab === '2d');
        if (elements.tab3d) elements.tab3d.classList.toggle('data-active', tab === '3d');
    };

    const addObject = () => {
        const type = elements.addType?.value;
        if (!type) {
            return;
        }

        state.plan.push({
            id: `floor-plan-object-${Date.now()}-${state.plan.length}`,
            name: TYPE_LABELS[type] || type,
            type,
            x: state.roomLength / 2 - 0.5,
            y: state.roomWidth / 2 - 0.5,
            width: type === 'cashier_desk' ? 0.7 : type === 'door' ? 0.9 : type === 'aisle' ? 1.2 : 1,
            height: type === 'cashier_desk' ? 1.0 : type === 'door' ? 2.1 : type === 'aisle' ? 1.2 : 0.9,
            rotation: 0,
            color: state.plan.find((item) => item.type === type)?.color || '#687EFF',
        });

        render();
        renderAnalysis(elements.objects, state);
    };

    const deleteSelected = () => {
        if (!state.selectedObjectId) {
            return;
        }

        state.plan = state.plan.filter((item) => String(item.id) !== String(state.selectedObjectId));
        state.selectedObjectId = null;
        render();
        renderAnalysis(elements.objects, state);
    };

    const rotateSelected = () => {
        const selected = state.plan.find((item) => String(item.id) === String(state.selectedObjectId));
        if (!selected) {
            return;
        }

        selected.rotation = (toNumber(selected.rotation, 0) + 90) % 360;
        render();
        renderAnalysis(elements.objects, state);
    };

    const downloadPng = () => {
        const svg = elements.svg;
        if (!svg) {
            return;
        }

        const clone = svg.cloneNode(true);
        clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        const serializer = new XMLSerializer();
        const source = serializer.serializeToString(clone);
        const image = new Image();
        const canvas = document.createElement('canvas');
        const scale = 2;

        image.onload = () => {
            canvas.width = Math.max(1, toNumber(state.roomLength, 0) * scale);
            canvas.height = Math.max(1, toNumber(state.roomWidth, 0) * scale);
            const context = canvas.getContext('2d');
            context.fillStyle = '#FFFFFF';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            canvas.toBlob((blob) => {
                if (!blob) {
                    return;
                }

                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = 'denah-2d-nivora.png';
                link.click();
                URL.revokeObjectURL(url);
            });
        };

        image.src = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(source)}`;
    };

    const downloadPdf = () => {
        const svg = elements.svg;
        if (!svg) {
            return;
        }

        const clone = svg.cloneNode(true);
        clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        const source = new XMLSerializer().serializeToString(clone);
        const html = `<!doctype html><html><head><meta charset="utf-8"><title>Denah 2D - Nivora</title><style>body{margin:0;background:#fff}svg{width:100%;height:auto}</style></head><body>${source}</body></html>`;
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        document.body.append(iframe);
        const frameWindow = iframe.contentWindow;
        const frameDocument = iframe.contentDocument;
        frameDocument.open();
        frameDocument.write(html);
        frameDocument.close();
        frameWindow.focus();
        frameWindow.print();
        setTimeout(() => frameWindow.close(), 1000);
    };

    loadLatestPlan();
}

function round2(value) {
    return Math.round(value * 100) / 100;
}
