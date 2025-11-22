import React from 'react'
import ReactDOM from 'react-dom/client'

function TestApp() {
  return (
    <div style={{ 
      minHeight: '100vh', 
      background: 'linear-gradient(135deg, #dbeafe 0%, #ffffff 50%, #e0e7ff 100%)',
      padding: '2rem'
    }}>
      <div style={{ 
        maxWidth: '80rem', 
        margin: '0 auto', 
        textAlign: 'center'
      }}>
        <h1 style={{ 
          fontSize: '2.5rem', 
          fontWeight: 'bold', 
          color: '#1f2937', 
          marginBottom: '1rem' 
        }}>
          📊 UMKM Template Viewer
        </h1>
        <p style={{ 
          color: '#4b5563', 
          fontSize: '1.1rem',
          marginBottom: '2rem'
        }}>
          Upload dan analisis template Excel UMKM dengan mudah. 
          Tidak memerlukan database - semua diproses di browser Anda.
        </p>
        
        <div style={{
          backgroundColor: 'white',
          padding: '2rem',
          borderRadius: '1rem',
          boxShadow: '0 10px 25px rgba(0,0,0,0.1)',
          border: '1px solid #e5e7eb'
        }}>
          <h2 style={{ 
            fontSize: '1.5rem', 
            fontWeight: '600', 
            color: '#374151',
            marginBottom: '1rem'
          }}>
            Test Component Loaded Successfully! ✅
          </h2>
          <p style={{ color: '#6b7280' }}>
            React dan styling berfungsi dengan baik.
          </p>
        </div>
      </div>
    </div>
  )
}

ReactDOM.createRoot(document.getElementById('app')).render(<TestApp />)