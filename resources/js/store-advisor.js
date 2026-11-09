import { apiMessage, apiRequest, escapeHtml, requireSession } from './nivora-api';

const SVG_NS = 'http://www.w3.org/2000/svg';

const STORE_TYPES = {
    kelontong: 'Kelontong',
    minimarket: 'Minimarket',
    sembako: 'Sembako',
    lainnya: 'Lainnya',
};

const DOOR_POSITIONS = {
    depan: 'Depan',
    kiri: 'Kiri',
    kanan: 'Kanan',
    belakang: 'Belakang',
};

const LAYOUT_CONCEPTS = {
    modern: 'Modern',
    minimalis: 'Minimalis',
    tradisional: 'Tradisional',
    efisien: 'Efisien',
};

const LAYOUT_PRIORITIES = {
    kapasitas_produk: 'Kapasitas produk',
    kenyamanan_pelanggan: 'Kenyamanan pelanggan',
    keseimbangan: 'Keseimbangan',
};

const TYPE_LABELS = {
    wall_rack: 'Rak Dinding',
    mid_rack: 'Rak Tengah',
    cashier_desk: 'Meja Kasir',
    door: 'Pintu Masuk',
    window: 'Jendela',
    stockroom: 'Gudang',
    storage: 'Area Penyimpanan',
    aisle: 'Lorong Pelanggan',
    zone: 'Area/Zona',
};

const TYPE_COLORS = {
    wall_rack: '#687EFF',
    mid_rack: '#98E4FF',
    cashier_desk: '#1F2544',
    door: '#F59E0B',
    window: '#38BDF8',
    stockroom: '#64748B',
    storage: '#B6FFFA',
    aisle: '#D7E4FF',
    zone: '#C7D2FE',
};

const PRIORITY_STYLES = {
    tinggi: 'bg-rose-100 text-rose-700',
    sedang: 'bg-amber-100 text-amber-800',
    rendah: 'bg-emerald-100 text-emerald-800',
};

function toNumber(value, fallback = 0) {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : fallback;
}

function round2(value) {
    return Math.round(value * 100) / 100;
}

function clamp(value, min, max) {
    return Math.min(max, Math.max(min, value));
}

function formatDateTime(value) {
    if (!value) {
        return '-';
    }

    const date = new Date(value);

    return Number.isNaN(date.getTime()) ? String(value) : date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
}

function isDarkColor(hex) {
    const value = String(hex || '').replace('#', '');
    if (value.length !== 6) {
        return false;
    }

    const r = parseInt(value.slice(0, 2), 16);
    const g = parseInt(value.slice(2, 4), 16);
    const b = parseInt(value.slice(4, 6), 16);

    return (0.2126 * r + 0.7152 * g + 0.0722 * b) < 140;
}

export function bootStoreAdvisor() {
    document.querySelectorAll('[data-store-advisor]').forEach((root) => initializeStoreAdvisor(root));
}

function initializeStoreAdvisor(root) {
    if (!requireSession()) {
        return;
    }

    const state = {
        design: null,
        activePlan: [],
        zoom: 1,
        editMode: false,
        selectedObjectId: null,
    };

    const q = (selector) => root.querySelector(selector);
    const qa = (selector) => Array.from(root.querySelectorAll(selector));

    const elements = {
        alert: q('[data-advisor-message]'),
        form: q('[data-advisor-form]'),
        length: q('[data-advisor-length]'),
        width: q('[data-advisor-width]'),
        area: q('[data-advisor-area]'),
        storeType: q('[data-advisor-store-type]'),
        storeTypeOtherWrap: q('[data-advisor-store-type-other-wrap]'),
        storeTypeOther: q('[data-advisor-store-type-other]'),
        doorPosition: q('[data-advisor-door-position]'),
        rackCount: q('[data-advisor-rack-count]'),
        layoutConcept: q('[data-advisor-layout-concept]'),
        layoutPriority: q('[data-advisor-layout-priority]'),
        submit: q('[data-advisor-submit]'),
        submitSpinner: q('[data-advisor-submit-spinner]'),
        submitLabel: q('[data-advisor-submit-label]'),
        result: q('[data-advisor-result]'),
        resultStore: q('[data-advisor-result-store]'),
        resultSize: q('[data-advisor-result-size]'),
        resultDate: q('[data-advisor-result-date]'),
        failed: q('[data-advisor-failed]'),
        failedMessage: q('[data-advisor-failed-message]'),
        retry: q('[data-advisor-retry]'),
        tabs: qa('[data-advisor-tab]'),
        panels: qa('[data-advisor-panel]'),
        assumptions: q('[data-advisor-assumptions]'),
        assumptionsList: q('[data-advisor-assumptions-list]'),
        recommendations: q('[data-advisor-recommendations]'),
        summary: q('[data-advisor-summary]'),
        pathsAreas: q('[data-advisor-paths-areas]'),
        regenerateButton: q('[data-advisor-regenerate]'),
        regenerateLabel: q('[data-advisor-regenerate-label]'),
        planSize: q('[data-advisor-plan-size]'),
        svg: q('[data-advisor-svg]'),
        objects: q('[data-advisor-objects]'),
        selectedName: q('[data-advisor-selected-name]'),
        selectedEstimated: q('[data-advisor-selected-estimated]'),
        selectedX: q('[data-advisor-selected-x]'),
        selectedY: q('[data-advisor-selected-y]'),
        selectedWidth: q('[data-advisor-selected-width]'),
        selectedHeight: q('[data-advisor-selected-height]'),
        rotate: q('[data-advisor-rotate]'),
        deleteObject: q('[data-advisor-delete]'),
        addType: q('[data-advisor-add-type]'),
        addButton: q('[data-advisor-add]'),
        edit: q('[data-advisor-edit]'),
        relayout: q('[data-advisor-relayout]'),
        save: q('[data-advisor-save]'),
        downloadPng: q('[data-advisor-download-png]'),
        downloadPdf: q('[data-advisor-download-pdf]'),
        zoomIn: q('[data-advisor-zoom-in]'),
        zoomOut: q('[data-advisor-zoom-out]'),
        zoomReset: q('[data-advisor-zoom-reset]'),
        zoomValue: q('[data-advisor-zoom-value]'),
        history: q('[data-advisor-history]'),
        historyEmpty: q('[data-advisor-history-empty]'),
        historyLoading: q('[data-advisor-history-loading]'),
        historyCount: q('[data-advisor-history-count]'),
    };

    const setMessage = (message, isError = false) => {
        if (!elements.alert) {
            return;
        }

        elements.alert.textContent = message;
        elements.alert.className = `rounded-2xl border px-4 py-3 text-sm ${isError ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-800'}`;
        elements.alert.classList.remove('hidden');
    };

    const clearMessage = () => elements.alert?.classList.add('hidden');

    const updateArea = () => {
        const length = toNumber(elements.length?.value, 0);
        const width = toNumber(elements.width?.value, 0);

        if (elements.area) {
            elements.area.textContent = (length > 0 && width > 0) ? String(round2(length * width)) : '0';
        }
    };

    elements.length?.addEventListener('input', updateArea);
    elements.width?.addEventListener('input', updateArea);

    elements.storeType?.addEventListener('change', () => {
        const isOther = elements.storeType.value === 'lainnya';
        elements.storeTypeOtherWrap?.classList.toggle('hidden', !isOther);
        if (elements.storeTypeOther) {
            elements.storeTypeOther.required = isOther;
            if (!isOther) {
                elements.storeTypeOther.value = '';
            }
        }
    });

    const switchTab = (tab) => {
        elements.tabs.forEach((button) => {
            const active = button.getAttribute('data-advisor-tab') === tab;
            button.setAttribute('aria-selected', String(active));
            button.className = active
                ? 'inline-flex min-h-11 items-center justify-center rounded-xl border border-[#1F2544] bg-[#1F2544] px-5 text-sm font-semibold text-white transition'
                : 'inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-5 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]';
        });

        elements.panels.forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-advisor-panel') !== tab);
        });
    };

    elements.tabs.forEach((button) => button.addEventListener('click', () => switchTab(button.getAttribute('data-advisor-tab'))));

    const roomLength = () => toNumber(state.design?.room_length_m, 0);
    const roomWidth = () => toNumber(state.design?.room_width_m, 0);

    const planObjectSize = (object) => {
        const rotation = toNumber(object.rotation, 0);
        const swapped = rotation === 90 || rotation === 270;

        return {
            width: swapped ? toNumber(object.height, 0) : toNumber(object.width, 0),
            height: swapped ? toNumber(object.width, 0) : toNumber(object.height, 0),
        };
    };

    const rackIndex = (object) => {
        if (!object || (object.type !== 'wall_rack' && object.type !== 'mid_rack')) {
            return null;
        }

        const racks = state.activePlan.filter((item) => item.type === 'wall_rack' || item.type === 'mid_rack');
        const index = racks.findIndex((item) => String(item.id) === String(object.id));

        return index >= 0 ? index + 1 : null;
    };

    const compactLabel = (object) => {
        const type = String(object?.type || '');
        const name = String(object?.name || TYPE_LABELS[type] || type || '').trim();
        const index = rackIndex(object);

        if (type === 'door') {
            return 'Pintu';
        }

        if (type === 'cashier_desk') {
            return 'Kasir';
        }

        if (type === 'stockroom') {
            return 'Gudang';
        }

        if (type === 'storage') {
            return 'Simpan';
        }

        if (type === 'aisle') {
            return 'Lorong';
        }

        if (type === 'zone') {
            return name !== '' && name.length <= 10 ? name : 'Area';
        }

        if (type === 'wall_rack') {
            return index ? `Rak D ${index}` : 'Rak D';
        }

        if (type === 'mid_rack') {
            return index ? `Rak T ${index}` : 'Rak T';
        }

        return name || TYPE_LABELS[type] || type;
    };

    const fitLabelText = (text, width, fontSize) => {
        const value = String(text || '').trim();
        if (value === '') {
            return '';
        }

        const maxChars = Math.max(2, Math.floor((width * 0.9) / Math.max(fontSize * 0.58, 0.08)));

        if (value.length <= maxChars) {
            return value;
        }

        if (maxChars <= 3) {
            return value.slice(0, maxChars);
        }

        return `${value.slice(0, maxChars - 1)}…`;
    };

    const labelSpec = (object, w, h) => {
        if (w < 0.35 || h < 0.22) {
            return null;
        }

        const fontSize = clamp(Math.min(w, h) * 0.26, 0.14, 0.34);
        const full = String(object?.name || TYPE_LABELS[object?.type] || object?.type || '').trim();
        const fullFits = full !== '' && full.length <= Math.floor((w * 0.9) / Math.max(fontSize * 0.58, 0.08)) && h >= fontSize * 1.9;
        const text = fullFits ? full : compactLabel(object);
        const fitted = fitLabelText(text, w, fontSize);

        if (fitted === '') {
            return null;
        }

        const boxWidth = Math.min(w * 0.92, fitted.length * fontSize * 0.62 + fontSize * 0.45);
        const boxHeight = fontSize * 1.45;

        if (boxWidth < fontSize * 1.2 || boxHeight > h * 0.92) {
            return null;
        }

        return { text: fitted, fontSize, boxWidth, boxHeight };
    };

    const gridLine = (x1, y1, x2, y2, reference) => {
        const line = document.createElementNS(SVG_NS, 'line');
        line.setAttribute('x1', String(x1));
        line.setAttribute('y1', String(y1));
        line.setAttribute('x2', String(x2));
        line.setAttribute('y2', String(y2));
        line.setAttribute('stroke', '#e3e8f4');
        line.setAttribute('stroke-width', String(Math.max(0.012, reference / 600)));
        return line;
    };

    const renderSvg = () => {
        const svg = elements.svg;
        if (!svg) {
            return;
        }

        const length = roomLength();
        const width = roomWidth();
        const zoom = state.zoom || 1;

        svg.innerHTML = '';

        if (length <= 0 || width <= 0) {
            svg.setAttribute('viewBox', '0 0 10 10');
            return;
        }

        const viewWidth = length / zoom;
        const viewHeight = width / zoom;
        const offsetX = (length - viewWidth) / 2;
        const offsetY = (width - viewHeight) / 2;
        svg.setAttribute('viewBox', `${offsetX} ${offsetY} ${viewWidth} ${viewHeight}`);

        const room = document.createElementNS(SVG_NS, 'rect');
        room.setAttribute('x', '0');
        room.setAttribute('y', '0');
        room.setAttribute('width', String(length));
        room.setAttribute('height', String(width));
        room.setAttribute('class', 'floor-plan-room');
        svg.append(room);

        for (let gx = 1; gx < Math.floor(length); gx += 1) {
            svg.append(gridLine(gx, 0, gx, width, length));
        }

        for (let gy = 1; gy < Math.floor(width); gy += 1) {
            svg.append(gridLine(0, gy, length, gy, width));
        }

        state.activePlan.forEach((object) => {
            const { width: w, height: h } = planObjectSize(object);
            const x = toNumber(object.x, 0);
            const y = toNumber(object.y, 0);
            const estimated = Boolean(object.estimated);
            const color = String(object.color || TYPE_COLORS[object.type] || '#94A3B8');

            const rect = document.createElementNS(SVG_NS, 'rect');
            rect.setAttribute('x', String(x));
            rect.setAttribute('y', String(y));
            rect.setAttribute('width', String(w));
            rect.setAttribute('height', String(h));
            rect.setAttribute('fill', color);
            rect.setAttribute('class', estimated ? 'floor-plan-object floor-plan-object-estimated' : 'floor-plan-object');
            rect.setAttribute('data-object-id', String(object.id ?? ''));
            svg.append(rect);

            const spec = labelSpec(object, w, h);
            if (spec) {
                const dark = isDarkColor(color);
                const labelBg = document.createElementNS(SVG_NS, 'rect');
                labelBg.setAttribute('x', String(x + w / 2 - spec.boxWidth / 2));
                labelBg.setAttribute('y', String(y + h / 2 - spec.boxHeight / 2));
                labelBg.setAttribute('width', String(spec.boxWidth));
                labelBg.setAttribute('height', String(spec.boxHeight));
                labelBg.setAttribute('rx', String(Math.min(spec.boxHeight / 2, 0.08)));
                labelBg.setAttribute('fill', dark ? 'rgba(255,255,255,0.16)' : 'rgba(255,255,255,0.72)');
                labelBg.setAttribute('stroke', dark ? 'rgba(255,255,255,0.18)' : 'rgba(31,37,68,0.10)');
                labelBg.setAttribute('stroke-width', String(Math.max(0.012, spec.fontSize * 0.06)));
                svg.append(labelBg);

                const label = document.createElementNS(SVG_NS, 'text');
                label.setAttribute('x', String(x + w / 2));
                label.setAttribute('y', String(y + h / 2));
                label.setAttribute('text-anchor', 'middle');
                label.setAttribute('dominant-baseline', 'central');
                label.setAttribute('class', 'floor-plan-object-label');
                label.setAttribute('font-size', String(spec.fontSize));
                label.setAttribute('font-weight', '600');
                label.setAttribute('fill', dark ? '#ffffff' : '#1f2544');
                label.textContent = spec.text;
                svg.append(label);
            }
        });
    };

    const findObject = (id) => state.activePlan.find((object) => String(object.id) === String(id));
    const selectedObject = () => (state.selectedObjectId ? findObject(state.selectedObjectId) : null);

    const syncSelectedEditor = () => {
        const object = selectedObject();

        if (elements.selectedName) {
            elements.selectedName.textContent = object ? String(object.name || object.type) : 'Pilih objek…';
        }

        elements.selectedEstimated?.classList.toggle('hidden', !object?.estimated);

        const fields = [
            [elements.selectedX, object?.x],
            [elements.selectedY, object?.y],
            [elements.selectedWidth, object?.width],
            [elements.selectedHeight, object?.height],
        ];

        fields.forEach(([input, value]) => {
            if (input) {
                input.value = value === undefined ? '' : String(toNumber(value, 0));
                input.disabled = !object;
            }
        });
    };

    const renderObjectChips = () => {
        if (!elements.objects) {
            return;
        }

        elements.objects.innerHTML = state.activePlan.map((object) => `
            <button type="button" data-object-id="${escapeHtml(object.id)}" class="floor-plan-object-chip${String(object.id) === String(state.selectedObjectId) ? ' is-selected' : ''}">
                <span class="floor-plan-object-dot" style="background-color:${escapeHtml(object.color || TYPE_COLORS[object.type] || '#94A3B8')}"></span>
                ${escapeHtml(object.name || TYPE_LABELS[object.type] || object.type)}
                ${object.estimated ? '<span class="rounded-full bg-amber-100 px-1.5 py-0.5 text-[9px] font-bold uppercase text-amber-800">AI</span>' : ''}
            </button>`).join('');
    };

    const renderPlan = () => {
        renderSvg();
        renderObjectChips();
        syncSelectedEditor();

        if (elements.zoomValue) {
            elements.zoomValue.textContent = `${Math.round((state.zoom || 1) * 100)}%`;
        }
    };

    const selectObject = (id) => {
        state.selectedObjectId = id;
        renderPlan();
    };

    elements.objects?.addEventListener('click', (event) => {
        const chip = event.target.closest('[data-object-id]');
        if (chip) {
            selectObject(chip.getAttribute('data-object-id'));
        }
    });

    const updateSelectedField = (key, input) => {
        const object = selectedObject();
        if (!object || !input) {
            return;
        }

        const value = toNumber(input.value, NaN);
        if (!Number.isFinite(value)) {
            return;
        }

        object[key] = key === 'width' || key === 'height' ? Math.max(0.1, value) : Math.max(0, value);
        object.estimated = false;
        renderPlan();
    };

    elements.selectedX?.addEventListener('input', (event) => updateSelectedField('x', event.target));
    elements.selectedY?.addEventListener('input', (event) => updateSelectedField('y', event.target));
    elements.selectedWidth?.addEventListener('input', (event) => updateSelectedField('width', event.target));
    elements.selectedHeight?.addEventListener('input', (event) => updateSelectedField('height', event.target));

    elements.rotate?.addEventListener('click', () => {
        const object = selectedObject();
        if (!object) {
            return;
        }

        object.rotation = (toNumber(object.rotation, 0) + 90) % 360;
        object.estimated = false;
        renderPlan();
    });

    elements.deleteObject?.addEventListener('click', () => {
        if (!state.selectedObjectId) {
            return;
        }

        state.activePlan = state.activePlan.filter((object) => String(object.id) !== String(state.selectedObjectId));
        state.selectedObjectId = null;
        renderPlan();
    });

    elements.addButton?.addEventListener('click', () => {
        const type = elements.addType?.value;
        if (!type) {
            return;
        }

        const length = roomLength() || 4;
        const width = roomWidth() || 4;
        const defaults = {
            wall_rack: [1, 0.9], mid_rack: [1, 0.9], cashier_desk: [0.7, 1],
            door: [0.9, 0.3], window: [1.2, 0.2], stockroom: [1.5, 1.5],
            storage: [1.5, 1], aisle: [1.2, Math.max(1, width - 1)], zone: [1.5, 1.5],
        };
        const [w, h] = defaults[type] ?? [1, 0.9];
        const id = `obj-manual-${Date.now()}`;

        state.activePlan.push({
            id,
            name: TYPE_LABELS[type] || type,
            type,
            x: round2(Math.max(0, length / 2 - w / 2)),
            y: round2(Math.max(0, width / 2 - h / 2)),
            width: w,
            height: h,
            rotation: 0,
            color: TYPE_COLORS[type] || '#94A3B8',
            estimated: false,
        });
        state.selectedObjectId = id;
        renderPlan();
    });

    elements.edit?.addEventListener('click', () => {
        state.editMode = !state.editMode;
        elements.edit.setAttribute('aria-pressed', String(state.editMode));
        elements.edit.textContent = state.editMode ? 'Selesai Edit' : 'Edit Denah';
        elements.edit.className = state.editMode
            ? 'inline-flex min-h-11 items-center justify-center rounded-xl border border-[#1F2544] bg-[#1F2544] px-4 text-sm font-semibold text-white transition'
            : 'inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]';
    });

    const svgPoint = (event) => {
        const svg = elements.svg;
        const matrix = svg?.getScreenCTM();

        if (!svg || !matrix) {
            return { x: 0, y: 0 };
        }

        const point = new DOMPoint(event.clientX, event.clientY).matrixTransform(matrix.inverse());

        return { x: point.x, y: point.y };
    };

    const objectAt = (x, y) => {
        for (let index = state.activePlan.length - 1; index >= 0; index -= 1) {
            const object = state.activePlan[index];
            const { width, height } = planObjectSize(object);
            const ox = toNumber(object.x, 0);
            const oy = toNumber(object.y, 0);

            if (x >= ox && x <= ox + width && y >= oy && y <= oy + height) {
                return object;
            }
        }

        return null;
    };

    elements.svg?.addEventListener('pointerdown', (event) => {
        const point = svgPoint(event);
        const object = objectAt(point.x, point.y);

        if (!object) {
            return;
        }

        selectObject(object.id);

        if (!state.editMode) {
            return;
        }

        event.preventDefault();
        const startX = toNumber(object.x, 0);
        const startY = toNumber(object.y, 0);

        const onMove = (moveEvent) => {
            const next = svgPoint(moveEvent);
            object.x = round2(clamp(startX + (next.x - point.x), 0, Math.max(0, roomLength() - planObjectSize(object).width)));
            object.y = round2(clamp(startY + (next.y - point.y), 0, Math.max(0, roomWidth() - planObjectSize(object).height)));
            object.estimated = false;
            renderPlan();
        };

        const onEnd = () => {
            window.removeEventListener('pointermove', onMove);
            window.removeEventListener('pointerup', onEnd);
        };

        window.addEventListener('pointermove', onMove);
        window.addEventListener('pointerup', onEnd, { once: true });
    });

    elements.zoomIn?.addEventListener('click', () => {
        state.zoom = clamp(round2(state.zoom * 1.25), 0.5, 4);
        renderPlan();
    });

    elements.zoomOut?.addEventListener('click', () => {
        state.zoom = clamp(round2(state.zoom * 0.8), 0.5, 4);
        renderPlan();
    });

    elements.zoomReset?.addEventListener('click', () => {
        state.zoom = 1;
        renderPlan();
    });

    const renderRecommendations = (analysis) => {
        const recommendations = Array.isArray(analysis?.recommendations) ? analysis.recommendations : [];

        if (!elements.recommendations) {
            return;
        }

        if (recommendations.length === 0) {
            elements.recommendations.innerHTML = '<p class="text-sm text-[#5e647f]">Belum ada rekomendasi. Jalankan analisis ulang.</p>';
            return;
        }

        elements.recommendations.innerHTML = recommendations.map((item) => {
            if (typeof item === 'string') {
                return `<div class="rounded-2xl border border-[#e7ebff] bg-white p-4 text-sm text-[#1f2544]">${escapeHtml(item)}</div>`;
            }

            const priority = String(item.priority || item.prioritas || 'sedang').toLowerCase();
            const badge = PRIORITY_STYLES[priority] || PRIORITY_STYLES.sedang;
            const title = escapeHtml(item.title || item.judul || 'Rekomendasi');
            const explanation = escapeHtml(item.explanation || item.penjelasan || item.description || '');
            const reason = escapeHtml(item.reason || item.alasan || '');

            return `
                <article class="rounded-2xl border border-[#e7ebff] bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <h5 class="text-sm font-semibold text-[#1f2544]">${title}</h5>
                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide ${badge}">Prioritas ${escapeHtml(priority)}</span>
                    </div>
                    ${explanation ? `<p class="mt-2 text-sm leading-6 text-[#1f2544]">${explanation}</p>` : ''}
                    ${reason ? `<p class="mt-2 text-xs leading-5 text-[#5e647f]"><span class="font-semibold text-[#1f2544]">Alasan:</span> ${reason}</p>` : ''}
                </article>`;
        }).join('');
    };

    const renderAssumptions = (analysis) => {
        const assumptions = Array.isArray(analysis?.assumptions) ? analysis.assumptions : [];

        if (!elements.assumptions || !elements.assumptionsList) {
            return;
        }

        elements.assumptions.classList.toggle('hidden', assumptions.length === 0);
        elements.assumptionsList.innerHTML = assumptions.map((item) => `<li>${escapeHtml(item)}</li>`).join('');
    };

    const renderSummary = (design, analysis) => {
        if (!elements.summary) {
            return;
        }

        const input = design.input_summary || {};
        const storeType = analysis?.store_type_label || STORE_TYPES[input.store_type] || input.store_type || '-';
        const door = analysis?.door_position_label || DOOR_POSITIONS[input.door_position] || input.door_position || '-';
        const rackCount = analysis?.rack_count ?? input.rack_count;
        const concept = analysis?.layout_concept_label || LAYOUT_CONCEPTS[input.layout_concept] || 'Otomatis';
        const priority = analysis?.layout_priority_label || LAYOUT_PRIORITIES[input.layout_priority] || 'Seimbang';
        const rows = [
            ['Jenis toko', storeType],
            ['Posisi pintu', door],
            ['Jumlah rak', rackCount !== null && rackCount !== undefined && rackCount !== '' ? `${toNumber(rackCount, 0)} rak` : 'Asumsi AI'],
            ['Konsep', concept],
            ['Prioritas', priority],
        ];

        elements.summary.innerHTML = rows.map(([term, value]) => `
            <div class="flex items-start justify-between gap-3 rounded-xl border border-[#e7ebff] bg-white px-3 py-2">
                <dt class="text-xs font-semibold uppercase tracking-wide text-[#5e647f]">${escapeHtml(term)}</dt>
                <dd class="text-right text-xs leading-5 text-[#1f2544]">${escapeHtml(value)}</dd>
            </div>`).join('');
    };

    const renderPathsAndAreas = (analysis) => {
        if (!elements.pathsAreas) {
            return;
        }

        const areas = Array.isArray(analysis?.areas) ? analysis.areas : [];
        const paths = Array.isArray(analysis?.customer_paths) ? analysis.customer_paths : [];
        const parts = [];

        if (areas.length > 0) {
            parts.push(`<div class="rounded-2xl border border-[#e7ebff] bg-white p-4"><p class="text-xs font-semibold uppercase tracking-wide text-[#5e647f]">Pembagian area</p><ul class="mt-2 list-disc space-y-1 pl-4 text-xs leading-5">${areas.map((area) => `<li><span class="font-semibold">${escapeHtml(area.name || 'Area')}</span>${area.notes ? ` — ${escapeHtml(area.notes)}` : ''}</li>`).join('')}</ul></div>`);
        }

        if (paths.length > 0) {
            parts.push(`<div class="rounded-2xl border border-[#e7ebff] bg-white p-4"><p class="text-xs font-semibold uppercase tracking-wide text-[#5e647f]">Jalur pelanggan</p><ul class="mt-2 list-disc space-y-1 pl-4 text-xs leading-5">${paths.map((path) => `<li><span class="font-semibold">${escapeHtml(path.name || 'Jalur')}</span>${path.description ? ` — ${escapeHtml(path.description)}` : ''}</li>`).join('')}</ul></div>`);
        }

        elements.pathsAreas.innerHTML = parts.join('') || '<p class="text-xs text-[#5e647f]">Detail jalur dan area ditampilkan pada denah 2D.</p>';
    };

    const applyDesign = (design, { focusResult = true } = {}) => {
        state.design = design;
        state.activePlan = Array.isArray(design.plan) ? design.plan.map((object) => ({ ...object })) : [];
        state.selectedObjectId = null;
        state.zoom = 1;
        state.editMode = false;

        if (elements.edit) {
            elements.edit.setAttribute('aria-pressed', 'false');
            elements.edit.textContent = 'Edit Denah';
            elements.edit.className = 'inline-flex min-h-11 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-4 text-sm font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]';
        }

        elements.result?.classList.remove('hidden');

        const analysis = design.analysis_result ?? {};
        const input = design.input_summary ?? {};

        if (elements.resultStore) {
            elements.resultStore.textContent = String(analysis.store_type_label || STORE_TYPES[input.store_type] || 'Toko');
        }

        if (elements.resultSize) {
            elements.resultSize.textContent = `${toNumber(design.room_length_m, 0)} m × ${toNumber(design.room_width_m, 0)} m · Luas ${toNumber(design.room_area_m2, 0)} m²`;
        }

        if (elements.resultDate) {
            elements.resultDate.textContent = `Dibuat ${formatDateTime(design.generated_at ?? design.created_at)}`;
        }

        const failedAnalysis = analysis?.status === 'failed';
        elements.failed?.classList.toggle('hidden', !failedAnalysis);

        if (failedAnalysis && elements.failedMessage) {
            elements.failedMessage.textContent = String(analysis.message ?? 'Terjadi kesalahan pada layanan AI.');
        }

        renderAssumptions(analysis);
        renderRecommendations(analysis);
        renderSummary(design, analysis);
        renderPathsAndAreas(analysis);

        if (elements.planSize) {
            elements.planSize.textContent = `${toNumber(design.room_length_m, 0)} m × ${toNumber(design.room_width_m, 0)} m · Luas ${toNumber(design.room_area_m2, 0)} m²`;
        }

        renderPlan();

        if (focusResult) {
            elements.result?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    };

    const setSubmitting = (submitting) => {
        if (!elements.submit) {
            return;
        }

        elements.submit.disabled = submitting;
        elements.submitSpinner?.classList.toggle('hidden', !submitting);

        if (elements.submitLabel) {
            elements.submitLabel.textContent = submitting ? 'AI sedang menganalisis toko Anda…' : 'Analisis dengan Gemini';
        }
    };

    elements.form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearMessage();

        const length = toNumber(elements.length?.value, 0);
        const width = toNumber(elements.width?.value, 0);
        const storeType = elements.storeType?.value || '';
        const doorPosition = elements.doorPosition?.value || '';
        const storeTypeOther = (elements.storeTypeOther?.value || '').trim();

        if (length < 0.5 || width < 0.5) {
            setMessage('Panjang dan lebar toko wajib diisi (minimal 0,5 m).', true);
            return;
        }

        if (!storeType) {
            setMessage('Jenis toko wajib dipilih.', true);
            return;
        }

        if (storeType === 'lainnya' && !storeTypeOther) {
            setMessage('Tuliskan jenis toko Anda.', true);
            return;
        }

        if (!doorPosition) {
            setMessage('Posisi pintu masuk wajib dipilih.', true);
            return;
        }

        const body = {
            room_length_m: length,
            room_width_m: width,
            store_type: storeType,
            door_position: doorPosition,
        };

        if (storeType === 'lainnya') {
            body.store_type_other = storeTypeOther;
        }

        const rackCount = (elements.rackCount?.value || '').trim();
        if (rackCount !== '') {
            body.rack_count = Math.max(0, Math.min(100, Math.trunc(toNumber(rackCount, 0))));
        }

        const layoutConcept = elements.layoutConcept?.value || '';
        const layoutPriority = elements.layoutPriority?.value || '';

        if (layoutConcept) {
            body.layout_concept = layoutConcept;
        }

        if (layoutPriority) {
            body.layout_priority = layoutPriority;
        }

        setSubmitting(true);

        try {
            const payload = await apiRequest('/ai/floor-plans', { method: 'POST', body: JSON.stringify(body) });
            const design = payload?.data;

            if (!design?.id) {
                throw new Error('Respons server tidak valid.');
            }

            applyDesign(design);
            setMessage(String(payload?.message ?? 'Denah 2D dan rekomendasi berhasil dibuat.'), false);
            switchTab('recommendations');
            loadHistory();
        } catch (error) {
            setMessage(apiMessage(error), true);

            if (error?.payload?.data?.design_id) {
                loadHistory();
            }
        } finally {
            setSubmitting(false);
        }
    });

    elements.relayout?.addEventListener('click', async () => {
        if (!state.design?.id) {
            return;
        }

        if (!window.confirm('Rapikan denah saat ini secara otomatis? Perubahan manual yang belum disimpan akan hilang.')) {
            return;
        }

        elements.relayout.disabled = true;
        const original = elements.relayout.textContent;
        elements.relayout.textContent = 'Merapikan…';

        try {
            const payload = await apiRequest(`/ai/floor-plans/${state.design.id}/relayout`, { method: 'POST', body: JSON.stringify({}) });
            if (payload?.data?.id) {
                applyDesign(payload.data, { focusResult: false });
                setMessage(String(payload?.message ?? 'Denah berhasil dirapikan otomatis.'), false);
                loadHistory();
            }
        } catch (error) {
            setMessage(apiMessage(error), true);
        } finally {
            elements.relayout.disabled = false;
            elements.relayout.textContent = original;
        }
    });

    elements.save?.addEventListener('click', async () => {
        if (!state.design?.id) {
            setMessage('Belum ada denah untuk disimpan.', true);
            return;
        }

        elements.save.disabled = true;
        const original = elements.save.textContent;
        elements.save.textContent = 'Menyimpan…';

        try {
            const payload = await apiRequest(`/ai/floor-plans/${state.design.id}`, {
                method: 'PUT',
                body: JSON.stringify({ plan: state.activePlan }),
            });
            state.design.status = payload?.data?.status ?? 'saved';
            setMessage(String(payload?.message ?? 'Denah berhasil disimpan.'), false);
            loadHistory();
        } catch (error) {
            setMessage(apiMessage(error), true);
        } finally {
            elements.save.disabled = false;
            elements.save.textContent = original;
        }
    });

    const regenerate = async () => {
        if (!state.design?.id) {
            setMessage('Tidak ada desain untuk dianalisis ulang.', true);
            return;
        }

        if (elements.regenerateButton) {
            elements.regenerateButton.disabled = true;
        }

        if (elements.regenerateLabel) {
            elements.regenerateLabel.textContent = 'Menganalisis ulang…';
        }

        try {
            const payload = await apiRequest(`/ai/floor-plans/${state.design.id}/regenerate`, {
                method: 'POST',
                body: JSON.stringify({}),
            });

            if (payload?.data?.id) {
                applyDesign(payload.data, { focusResult: false });
                setMessage(String(payload?.message ?? 'Analisis berhasil dijalankan ulang.'), false);
                loadHistory();
            }
        } catch (error) {
            setMessage(apiMessage(error), true);
        } finally {
            if (elements.regenerateButton) {
                elements.regenerateButton.disabled = false;
            }

            if (elements.regenerateLabel) {
                elements.regenerateLabel.textContent = 'Analisis Ulang';
            }
        }
    };

    elements.regenerateButton?.addEventListener('click', regenerate);
    elements.retry?.addEventListener('click', regenerate);

    const buildExportSvg = () => {
        const length = roomLength();
        const width = roomWidth();
        const padX = 2;
        const titleSpace = 5;
        const totalWidth = length + padX * 2;
        const totalHeight = width + titleSpace + 2;
        const analysis = state.design?.analysis_result ?? {};
        const input = state.design?.input_summary ?? {};
        const storeType = String(analysis.store_type_label || STORE_TYPES[input.store_type] || '-');
        const rackCount = analysis.rack_count ?? input.rack_count;
        const rackInfo = rackCount !== null && rackCount !== undefined && rackCount !== '' ? ` · ${toNumber(rackCount, 0)} rak` : '';
        const date = new Date().toLocaleDateString('id-ID', { dateStyle: 'long' });

        const parts = [
            `<rect x="0" y="0" width="${totalWidth}" height="${totalHeight}" fill="#ffffff"/>`,
            `<text x="${totalWidth / 2}" y="1.6" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="1.15" font-weight="700" fill="#1f2544">DENAH 2D TOKO — TAMPAK ATAS</text>`,
            `<text x="${totalWidth / 2}" y="3.2" text-anchor="middle" font-family="Poppins, Arial, sans-serif" font-size="0.72" fill="#5e647f">Jenis ${escapeHtml(storeType)}${escapeHtml(rackInfo)} · ${length} m × ${width} m · Luas ${round2(length * width)} m² · ${escapeHtml(date)}</text>`,
            `<g transform="translate(${padX}, ${titleSpace})">`,
            `<rect x="0" y="0" width="${length}" height="${width}" fill="#ffffff" stroke="#1f2544" stroke-width="0.06"/>`,
        ];

        state.activePlan.forEach((object) => {
            const { width: w, height: h } = planObjectSize(object);
            const x = toNumber(object.x, 0);
            const y = toNumber(object.y, 0);
            const color = String(object.color || TYPE_COLORS[object.type] || '#94A3B8');
            const dash = object.estimated ? ' stroke-dasharray="0.18 0.12" stroke-opacity="0.9" fill-opacity="0.55"' : '';

            parts.push(`<rect x="${x}" y="${y}" width="${w}" height="${h}" fill="${escapeHtml(color)}" stroke="#1f2544" stroke-width="0.04"${dash}/>`);

            const spec = labelSpec(object, w, h);
            if (spec) {
                const dark = isDarkColor(color);
                const bgFill = dark ? 'rgba(255,255,255,0.16)' : 'rgba(255,255,255,0.72)';
                const bgStroke = dark ? 'rgba(255,255,255,0.18)' : 'rgba(31,37,68,0.10)';
                const textColor = dark ? '#ffffff' : '#1f2544';
                parts.push(`<rect x="${x + w / 2 - spec.boxWidth / 2}" y="${y + h / 2 - spec.boxHeight / 2}" width="${spec.boxWidth}" height="${spec.boxHeight}" rx="${Math.min(spec.boxHeight / 2, 0.08)}" fill="${bgFill}" stroke="${bgStroke}" stroke-width="${Math.max(0.012, spec.fontSize * 0.06)}"/>`);
                parts.push(`<text x="${x + w / 2}" y="${y + h / 2}" text-anchor="middle" dominant-baseline="central" font-family="Poppins, Arial, sans-serif" font-size="${spec.fontSize}" font-weight="600" fill="${textColor}">${escapeHtml(spec.text)}</text>`);
            }
        });

        parts.push('</g>');

        return `<svg xmlns="${SVG_NS}" viewBox="0 0 ${totalWidth} ${totalHeight}">${parts.join('')}</svg>`;
    };

    const downloadBlob = (blob, filename) => {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        URL.revokeObjectURL(url);
    };

    elements.downloadPng?.addEventListener('click', () => {
        if (!state.design || roomLength() <= 0) {
            setMessage('Belum ada denah untuk diunduh.', true);
            return;
        }

        const source = buildExportSvg();
        const image = new Image();

        image.onload = () => {
            const scale = 1600 / image.naturalWidth;
            const canvas = document.createElement('canvas');
            canvas.width = 1600;
            canvas.height = Math.max(1, Math.round(image.naturalHeight * scale));
            const context = canvas.getContext('2d');
            context.fillStyle = '#ffffff';
            context.fillRect(0, 0, canvas.width, canvas.height);
            context.drawImage(image, 0, 0, canvas.width, canvas.height);
            canvas.toBlob((blob) => blob && downloadBlob(blob, 'denah-2d-nivora.png'), 'image/png');
        };

        image.onerror = () => setMessage('Gagal membuat PNG denah.', true);
        image.src = `data:image/svg+xml;charset=utf-8,${encodeURIComponent(source)}`;
    });

    elements.downloadPdf?.addEventListener('click', () => {
        if (!state.design || roomLength() <= 0) {
            setMessage('Belum ada denah untuk diunduh.', true);
            return;
        }

        const source = buildExportSvg();
        const landscape = roomLength() >= roomWidth();
        const html = `<!doctype html><html lang="id"><head><meta charset="utf-8"><title>Denah 2D - Nivora</title><style>@page{size:${landscape ? 'A4 landscape' : 'A4 portrait'};margin:10mm}body{margin:0;display:flex;justify-content:center;align-items:flex-start}svg{width:100%;height:auto;max-height:100%}</style></head><body>${source}</body></html>`;
        const iframe = document.createElement('iframe');
        iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0';
        document.body.append(iframe);
        const frameDocument = iframe.contentWindow.document;
        frameDocument.open();
        frameDocument.write(html);
        frameDocument.close();
        iframe.contentWindow.focus();
        setTimeout(() => {
            iframe.contentWindow.print();
            setTimeout(() => iframe.remove(), 2000);
        }, 250);
    });

    const statusBadge = (design) => {
        const failed = design?.analysis_result?.status === 'failed';

        if (failed) {
            return '<span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-rose-700">Gagal</span>';
        }

        return design.status === 'saved'
            ? '<span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-800">Tersimpan</span>'
            : '<span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-800">Draf</span>';
    };

    const renderHistory = (designs) => {
        if (!elements.history) {
            return;
        }

        elements.historyLoading?.classList.add('hidden');
        elements.historyEmpty?.classList.toggle('hidden', designs.length > 0);

        if (elements.historyCount) {
            elements.historyCount.textContent = `${designs.length} desain`;
        }

        elements.history.innerHTML = designs.map((design) => {
            const input = design.input_summary || {};
            const analysis = design.analysis_result || {};
            const label = analysis.store_type_label || STORE_TYPES[input.store_type] || design.concept_label || 'Toko';

            return `
            <article class="overflow-hidden rounded-[24px] border border-[#e7ebff] bg-white shadow-sm transition hover:shadow-md">
                <div class="flex h-24 items-center justify-center bg-gradient-to-br from-[#eef3ff] to-[#f7f9ff] px-4 text-center">
                    <p class="text-sm font-semibold text-[#1f2544]">Denah 2D ${escapeHtml(label)}</p>
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-semibold text-[#1f2544]">${escapeHtml(label)}</p>
                        ${statusBadge(design)}
                    </div>
                    <p class="mt-1 text-xs text-[#5e647f]">${toNumber(design.room_length_m, 0)} m × ${toNumber(design.room_width_m, 0)} m · Luas ${toNumber(design.room_area_m2, 0)} m²</p>
                    <p class="mt-0.5 text-[11px] text-[#7b8299]">Pintu: ${escapeHtml(analysis.door_position_label || DOOR_POSITIONS[input.door_position] || '-')} · ${escapeHtml(formatDateTime(design.generated_at ?? design.created_at))}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <button type="button" data-history-open="${design.id}" class="inline-flex min-h-10 flex-1 items-center justify-center rounded-xl border border-[#dfe7ff] bg-white px-3 text-xs font-semibold text-[#1f2544] transition hover:bg-[#f7f9ff]">Buka</button>
                        <button type="button" data-history-delete="${design.id}" class="inline-flex min-h-10 items-center justify-center rounded-xl border border-rose-200 bg-white px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-50" aria-label="Hapus desain">Hapus</button>
                    </div>
                </div>
            </article>`;
        }).join('');
    };

    const loadHistory = async () => {
        try {
            const payload = await apiRequest('/ai/floor-plans');
            const designs = Array.isArray(payload?.data?.data) ? payload.data.data : [];
            renderHistory(designs);
        } catch (error) {
            elements.historyLoading?.classList.add('hidden');
            setMessage(apiMessage(error), true);
        }
    };

    elements.history?.addEventListener('click', async (event) => {
        const openButton = event.target.closest('[data-history-open]');
        const deleteButton = event.target.closest('[data-history-delete]');

        if (openButton) {
            const id = openButton.getAttribute('data-history-open');
            openButton.disabled = true;

            try {
                const payload = await apiRequest(`/ai/floor-plans/${id}`);

                if (payload?.data?.id) {
                    applyDesign(payload.data);
                    clearMessage();
                }
            } catch (error) {
                setMessage(apiMessage(error), true);
            } finally {
                openButton.disabled = false;
            }

            return;
        }

        if (deleteButton) {
            const id = deleteButton.getAttribute('data-history-delete');

            if (!window.confirm('Hapus desain ini? Tindakan ini tidak dapat dibatalkan.')) {
                return;
            }

            deleteButton.disabled = true;

            try {
                await apiRequest(`/ai/floor-plans/${id}`, { method: 'DELETE' });

                if (String(state.design?.id) === String(id)) {
                    state.design = null;
                    elements.result?.classList.add('hidden');
                }

                setMessage('Desain dihapus.', false);
                loadHistory();
            } catch (error) {
                setMessage(apiMessage(error), true);
                deleteButton.disabled = false;
            }
        }
    });

    updateArea();
    renderPlan();
    loadHistory();
}
