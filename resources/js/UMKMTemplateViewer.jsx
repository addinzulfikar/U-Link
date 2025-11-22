import React, { useEffect, useState, useRef } from 'react'
import * as XLSX from 'xlsx'

export default function UMKMTemplateViewer() {
  const [workbook, setWorkbook] = useState(null)
  const [sheetNames, setSheetNames] = useState([])
  const [currentSheet, setCurrentSheet] = useState(null)
  const [rows, setRows] = useState([])
  const [headers, setHeaders] = useState([])
  const [query, setQuery] = useState('')
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(false)
  const [error, setError] = useState(null)
  const pageSize = 25
  const fileInputRef = useRef()

  function setLoadedWorkbook(wb) {
    try {
      setWorkbook(wb)
      setSheetNames(wb.SheetNames || [])
      const first = wb.SheetNames && wb.SheetNames[0]
      if (first) selectSheet(first, wb)
      setError(null)
    } catch (err) {
      setError('Error loading workbook: ' + err.message)
    }
  }

  function handleFile(e) {
    const f = e.target.files && e.target.files[0]
    if (!f) return
    
    setLoading(true)
    setError(null)
    
    const reader = new FileReader()
    reader.onload = (evt) => {
      try {
        const data = evt.target.result
        const wb = XLSX.read(data, { 
          type: 'array',
          cellText: false,
          cellDates: true,
          raw: false
        })
        setLoadedWorkbook(wb)
      } catch (err) {
        setError('Error reading file: ' + err.message)
      } finally {
        setLoading(false)
      }
    }
    reader.onerror = () => {
      setError('Error reading file')
      setLoading(false)
    }
    reader.readAsArrayBuffer(f)
  }

  function selectSheet(name, wb = workbook) {
    if (!wb || !name) return
    
    try {
      const sheet = wb.Sheets[name]
      if (!sheet) {
        setError('Sheet not found: ' + name)
        return
      }

      // Better data extraction with proper handling of different cell types
      const json = XLSX.utils.sheet_to_json(sheet, { 
        defval: '',
        raw: false,
        dateNF: 'yyyy-mm-dd'
      })
      
      const hdrs = getHeadersFromSheet(sheet)
      setCurrentSheet(name)
      setRows(json)
      setHeaders(hdrs)
      setPage(1)
      setError(null)
    } catch (err) {
      setError('Error processing sheet: ' + err.message)
    }
  }

  function getHeadersFromSheet(sheet) {
    try {
      const range = XLSX.utils.decode_range(sheet['!ref'] || 'A1:A1')
      if (!range) return []
      
      const firstRow = range.s.r
      const headers = []
      
      for (let C = range.s.c; C <= range.e.c; ++C) {
        const cellAddress = { c: C, r: firstRow }
        const cellRef = XLSX.utils.encode_cell(cellAddress)
        const cell = sheet[cellRef]
        
        let headerValue = ''
        if (cell) {
          if (cell.w) headerValue = cell.w
          else if (cell.v !== undefined) headerValue = String(cell.v)
        }
        
        headers.push(headerValue || `Column ${C+1}`)
      }
      return headers
    } catch (err) {
      console.error('Error getting headers:', err)
      return []
    }
  }

  function filteredRows() {
    if (!query) return rows
    const q = query.toLowerCase()
    return rows.filter(r => 
      headers.some(h => {
        const value = r[h]
        return value !== null && value !== undefined && 
               String(value).toLowerCase().includes(q)
      })
    )
  }

  function visibleRows() {
    const fr = filteredRows()
    const start = (page - 1) * pageSize
    return fr.slice(start, start + pageSize)
  }

  function downloadCSV() {
    if (!currentSheet || !rows.length) return
    try {
      const ws = XLSX.utils.json_to_sheet(rows)
      const csv = XLSX.utils.sheet_to_csv(ws)
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `${currentSheet || 'sheet'}.csv`
      a.click()
      URL.revokeObjectURL(url)
    } catch (err) {
      setError('Error downloading CSV: ' + err.message)
    }
  }

  function downloadJSON() {
    if (!currentSheet || !rows.length) return
    try {
      const data = JSON.stringify(rows, null, 2)
      const blob = new Blob([data], { type: 'application/json' })
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = `${currentSheet || 'sheet'}.json`
      a.click()
      URL.revokeObjectURL(url)
    } catch (err) {
      setError('Error downloading JSON: ' + err.message)
    }
  }

  const totalPages = Math.ceil(filteredRows().length / pageSize)

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
      <div className="container mx-auto px-4 py-8 max-w-7xl">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-4xl font-bold text-gray-800 mb-2">
            📊 UMKM Template Viewer
          </h1>
          <p className="text-gray-600 max-w-2xl mx-auto">
            Upload dan analisis template Excel UMKM dengan mudah. 
            Tidak memerlukan database - semua diproses di browser Anda.
          </p>
        </div>

        {/* Error Alert */}
        {error && (
          <div className="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg">
            <div className="flex">
              <div className="flex-shrink-0">
                <span className="text-red-400">⚠️</span>
              </div>
              <div className="ml-3">
                <p className="text-sm text-red-700">{error}</p>
              </div>
              <div className="ml-auto">
                <button
                  onClick={() => setError(null)}
                  className="text-red-400 hover:text-red-600"
                >
                  ✕
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Control Panel */}
        <div className="bg-white rounded-2xl shadow-lg p-6 mb-8 border border-gray-100">
          <div className="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
            <div className="flex flex-wrap gap-3">
              <label className="relative">
                <input
                  ref={fileInputRef}
                  onChange={handleFile}
                  type="file"
                  accept=".xlsx,.xls"
                  className="hidden"
                  disabled={loading}
                />
                <button
                  className="flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 font-medium shadow-md disabled:opacity-50"
                  onClick={() => fileInputRef.current?.click()}
                  disabled={loading}
                >
                  {loading ? '⏳' : '📁'} Upload Excel
                </button>
              </label>

              <button
                className="flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 font-medium shadow-md disabled:opacity-50"
                onClick={downloadCSV}
                disabled={!currentSheet || !rows.length}
              >
                💾 Download CSV
              </button>

              <button
                className="flex items-center gap-2 px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200 font-medium shadow-md disabled:opacity-50"
                onClick={downloadJSON}
                disabled={!currentSheet || !rows.length}
              >
                📋 Download JSON
              </button>
            </div>

            <div className="w-full lg:w-auto">
              <div className="relative">
                <input
                  value={query}
                  onChange={e => { setQuery(e.target.value); setPage(1) }}
                  placeholder="🔍 Cari data..."
                  className="w-full lg:w-80 px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                />
              </div>
            </div>
          </div>
        </div>

        {/* Main Content */}
        <div className="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
          {/* Sheet Tabs */}
          {sheetNames.length > 0 && (
            <div className="border-b border-gray-200 p-4 bg-gray-50">
              <div className="flex items-center justify-between mb-3">
                <h3 className="text-lg font-semibold text-gray-800">Sheet Tabs</h3>
                <div className="text-sm text-gray-500 bg-white px-3 py-1 rounded-full">
                  {rows.length} baris • {headers.length} kolom
                </div>
              </div>
              <div className="flex gap-2 flex-wrap">
                {sheetNames.map(name => (
                  <button
                    key={name}
                    className={`px-4 py-2 rounded-lg font-medium transition-all duration-200 ${
                      name === currentSheet
                        ? 'bg-blue-600 text-white shadow-md'
                        : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:border-gray-400'
                    }`}
                    onClick={() => selectSheet(name)}
                  >
                    {name}
                  </button>
                ))}
              </div>
            </div>
          )}

          {/* Data Table */}
          <div className="p-6">
            {loading ? (
              <div className="text-center py-16">
                <div className="inline-flex items-center gap-3 text-blue-600">
                  <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                  <span className="text-lg font-medium">Memuat file...</span>
                </div>
              </div>
            ) : rows.length === 0 ? (
              <div className="text-center py-16">
                <div className="text-6xl mb-4">📊</div>
                <h3 className="text-xl font-semibold text-gray-700 mb-2">Belum ada data</h3>
                <p className="text-gray-500">Upload file Excel untuk mulai melihat data</p>
              </div>
            ) : (
              <div>
                <div className="overflow-x-auto rounded-lg border border-gray-200">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr>
                        {headers.map((h, i) => (
                          <th
                            key={i}
                            className="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                          >
                            {h}
                          </th>
                        ))}
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                      {visibleRows().map((r, ridx) => (
                        <tr
                          key={ridx}
                          className={`hover:bg-gray-50 transition-colors duration-150 ${
                            ridx % 2 === 0 ? 'bg-white' : 'bg-gray-25'
                          }`}
                        >
                          {headers.map((h, cidx) => (
                            <td
                              key={cidx}
                              className="px-6 py-4 whitespace-nowrap text-sm text-gray-900 max-w-xs truncate"
                              title={String(r[h] ?? '')}
                            >
                              {String(r[h] ?? '')}
                            </td>
                          ))}
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>

                {/* Pagination */}
                <div className="mt-6 flex items-center justify-between">
                  <div className="text-sm text-gray-700">
                    Menampilkan{' '}
                    <span className="font-medium">
                      {Math.min(filteredRows().length, (page - 1) * pageSize + 1)}
                    </span>{' '}
                    -{' '}
                    <span className="font-medium">
                      {Math.min(filteredRows().length, page * pageSize)}
                    </span>{' '}
                    dari{' '}
                    <span className="font-medium">{filteredRows().length}</span> hasil
                  </div>

                  <div className="flex items-center gap-2">
                    <button
                      onClick={() => setPage(Math.max(1, page - 1))}
                      disabled={page === 1}
                      className="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                      ← Sebelumnya
                    </button>

                    <div className="flex items-center gap-1">
                      {[...Array(Math.min(5, totalPages))].map((_, i) => {
                        const pageNum = Math.max(1, Math.min(totalPages - 4, page - 2)) + i
                        if (pageNum > totalPages) return null
                        
                        return (
                          <button
                            key={pageNum}
                            onClick={() => setPage(pageNum)}
                            className={`px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 ${
                              pageNum === page
                                ? 'bg-blue-600 text-white'
                                : 'text-gray-700 hover:bg-gray-100'
                            }`}
                          >
                            {pageNum}
                          </button>
                        )
                      })}
                    </div>

                    <button
                      onClick={() => setPage(Math.min(totalPages, page + 1))}
                      disabled={page >= totalPages}
                      className="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                      Selanjutnya →
                    </button>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Footer */}
        <div className="text-center mt-8 text-gray-500">
          <p className="text-sm">
            🔒 Semua data diproses di browser Anda - tidak ada data yang dikirim ke server
          </p>
        </div>
      </div>
    </div>
  )
}
