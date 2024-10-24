import { useState } from 'react'
import { Input } from "@/components/ui/input"
import { Checkbox } from "@/components/ui/checkbox"
import { ChevronDown } from "lucide-react"

export default function ProductFilter() {
  const [searchTerm, setSearchTerm] = useState('')
  const [openSections, setOpenSections] = useState({
    category: true,
    brand: true,
    price: true,
    rating: true
  })

  const toggleSection = (section: keyof typeof openSections) => {
    setOpenSections(prev => ({ ...prev, [section]: !prev[section] }))
  }

  return (
    <div className="w-64 p-4 bg-white shadow-md">
      <h2 className="text-xl font-bold mb-4">VESTUARIO HOMBRE 40</h2>
      <p className="text-sm text-gray-600 mb-4">Resultados (352)</p>

      <div className="mb-6">
        <div className="flex justify-between items-center mb-2 cursor-pointer" onClick={() => toggleSection('category')}>
          <h3 className="font-semibold">Categoría</h3>
          <ChevronDown className={`transform transition-transform ${openSections.category ? 'rotate-180' : ''}`} />
        </div>
        {openSections.category && (
          <div className="space-x-2">
            <span className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Especiales</span>
            <span className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Moda y accesorios</span>
            <span className="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">Maletería y viajes</span>
          </div>
        )}
      </div>

      <div className="mb-6">
        <div className="flex justify-between items-center mb-2 cursor-pointer" onClick={() => toggleSection('brand')}>
          <h3 className="font-semibold">Marca</h3>
          <ChevronDown className={`transform transition-transform ${openSections.brand ? 'rotate-180' : ''}`} />
        </div>
        {openSections.brand && (
          <>
            <Input
              type="text"
              placeholder="Buscar..."
              value={searchTerm}
              onChange={(e) => setSearchTerm(e.target.value)}
              className="mb-2"
            />
            {['Adidas Originals', 'Basement', 'Bearcliff', 'Benetton', 'Calvin Klein', 'Cascais', 'Christian Lacroix'].map((brand) => (
              <div key={brand} className="flex items-center mb-2">
                <Checkbox id={brand} />
                <label htmlFor={brand} className="ml-2 text-sm">{brand}</label>
              </div>
            ))}
            <button className="text-sm text-blue-600 mt-2">+ Ver más</button>
          </>
        )}
      </div>

      <div className="mb-6">
        <div className="flex justify-between items-center mb-2 cursor-pointer" onClick={() => toggleSection('price')}>
          <h3 className="font-semibold">Precio</h3>
          <ChevronDown className={`transform transition-transform ${openSections.price ? 'rotate-180' : ''}`} />
        </div>
        {openSections.price && (
          <>
            {['Hasta s/ 50', 'S/ 50 - s/ 100', 'S/ 100 - s/ 250', 'S/ 250 - s/ 500'].map((range) => (
              <div key={range} className="flex items-center mb-2">
                <Checkbox id={range} />
                <label htmlFor={range} className="ml-2 text-sm">{range}</label>
              </div>
            ))}
          </>
        )}
      </div>

      <div>
        <div className="flex justify-between items-center mb-2 cursor-pointer" onClick={() => toggleSection('rating')}>
          <h3 className="font-semibold">Calificación del producto</h3>
          <ChevronDown className={`transform transition-transform ${openSections.rating ? 'rotate-180' : ''}`} />
        </div>
        {openSections.rating && (
          <div>
            {/* Aquí puedes agregar las opciones de calificación si las necesitas */}
            <p className="text-sm text-gray-600">Opciones de calificación irían aquí</p>
          </div>
        )}
      </div>
    </div>
  )
}