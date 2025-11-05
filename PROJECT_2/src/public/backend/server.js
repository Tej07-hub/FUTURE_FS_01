import express from 'express';
import mongoose from 'mongoose';
import cors from 'cors';
import dotenv from 'dotenv';

dotenv.config();

const app = express();

// Middleware
app.use(cors());
app.use(express.json());

// Test route
app.get('/', (req, res) => {
  res.json({ message: 'E-commerce API is running!' });
});

// Products route with database connection
app.get('/api/products', async (req, res) => {
  try {
    // Connect to MongoDB
    await mongoose.connect(process.env.MONGODB_URI);
    console.log('Connected to MongoDB');
    
    // Define a simple product schema
    const productSchema = new mongoose.Schema({
      name: String,
      price: Number,
      category: String,
      image: String,
      description: String
    });
    
    const Product = mongoose.model('Product', productSchema);
    
    // Check if products exist, if not create sample data
    let products = await Product.find();
    
    if (products.length === 0) {
      // Create sample products
      const sampleProducts = [
        {
          name: 'Wireless Headphones',
          price: 299,
          category: 'electronics',
          image: 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=400&h=400&fit=crop',
          description: 'High-quality wireless headphones with noise cancellation'
        },
        {
          name: 'Smart Watch',
          price: 1549,
          category: 'electronics',
          image: 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=400&h=400&fit=crop',
          description: 'Feature-rich smartwatch with health monitoring'
        }
      ];
      
      await Product.insertMany(sampleProducts);
      products = await Product.find();
      console.log('Sample products created');
    }
    
    res.json(products);
  } catch (error) {
    console.error('Database error:', error);
    res.status(500).json({ error: 'Database connection failed' });
  }
});

// Start server
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
  console.log(`🚀 Server running on port ${PORT}`);
  console.log(`📝 API: http://localhost:${PORT}`);
});